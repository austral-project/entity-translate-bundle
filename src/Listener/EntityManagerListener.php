<?php

/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityTranslateBundle\Listener;

use Austral\Contracts\Translate\TranslateChildInterface;
use Austral\Contracts\Translate\TranslateMasterInterface;
use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\EntityBundle\Event\EntityManagerMappingEvent;
use Austral\ToolsBundle\AustralTools;

/**
 * Austral EntityManager Listener.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class EntityManagerListener
{
  /**
   * @param EntityManagerMappingEvent<T> $mappingAssociationEvent
   */
  public function mapping(EntityManagerMappingEvent $mappingAssociationEvent): void
  {
    if (($entityClass = $mappingAssociationEvent->getEntityManager()->getClass()) && is_a($entityClass, TranslateMasterInterface::class, true))
    {
      $fieldsMapping = $mappingAssociationEvent->getFieldsMapping();
      $translateField = AustralTools::valueByKey($fieldsMapping, 'translates', []);
      if ($translateTargetEntity = AustralTools::valueByKey($translateField, 'targetEntity', null))
      {
        $em = $mappingAssociationEvent->getEntityManager()->getDoctrineEntityManager();
        $fieldsInTranslateEntity = $em->getClassMetadata($translateTargetEntity)->fieldMappings;
        foreach ($fieldsInTranslateEntity as $name => $values)
        {
          $fieldsInTranslateEntity[$name]['leftJoin'] = [
            'alias' => 'translates',
            'entity' => $translateField['targetEntity'],
          ];
        }
        $fieldsMapping = array_merge(
          $fieldsMapping,
          $fieldsInTranslateEntity,
          $em->getClassMetadata($translateTargetEntity)->associationMappings
        );
      }
      $mappingAssociationEvent->setFieldsMapping($fieldsMapping);
    }
  }

  /**
   * @param EntityManagerEvent<T> $entityManagerEvent
   *
   * @throws \Exception
   */
  public function duplicate(EntityManagerEvent $entityManagerEvent): void
  {
    if ($sourceObject = $entityManagerEvent->getSourceObject())
    {
      if (is_a($sourceObject, TranslateMasterInterface::class, true))
      {
        /** @var TranslateMasterInterface<T> $object */
        $object = $entityManagerEvent->getObject();
        $object->removeAllTranslates();

        /** @var TranslateChildInterface<T> $translate */
        foreach ($sourceObject->getTranslates() as $translate)
        {
          /** @var TranslateChildInterface<T> $duplicateTranslate */
          $duplicateTranslate = $entityManagerEvent->getEntityManager()->duplicate($translate);
          $object->addTranslateByLanguage($duplicateTranslate);

          $entityManagerEvent->getEntityManager()->update($duplicateTranslate, false);
        }
      }
    }
  }
}
