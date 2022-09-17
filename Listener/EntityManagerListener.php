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

use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\EntityBundle\Event\EntityManagerMappingEvent;

use Austral\ToolsBundle\AustralTools;

/**
 * Austral EntityManager Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerListener
{

  /**
   * @param EntityManagerMappingEvent $mappingAssociationEvent
   */
  public function mapping(EntityManagerMappingEvent $mappingAssociationEvent)
  {
    if(AustralTools::usedImplements($mappingAssociationEvent->getEntityManager()->getClass(),TranslateMasterInterface::class))
    {
      $fieldsMapping = $mappingAssociationEvent->getFieldsMapping();
      $translateField = AustralTools::getValueByKey($fieldsMapping, "translates", array());
      if($translateTargetEntity = AustralTools::getValueByKey($translateField, "targetEntity", null))
      {
        $em = $mappingAssociationEvent->getEntityManager()->getDoctrineEntityManager();
        $fieldsInTranslateEntity = $em->getClassMetadata($translateTargetEntity)->fieldMappings;
        foreach($fieldsInTranslateEntity as $name => $values)
        {
          $fieldsInTranslateEntity[$name]["leftJoin"] = array(
            "alias"   =>  "translates",
            "entity"  =>  $translateField["targetEntity"]
          );
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
   * @param EntityManagerEvent $entityManagerEvent
   * @throws \Exception
   */
  public function duplicate(EntityManagerEvent $entityManagerEvent)
  {
    /** @var EntityInterface|TranslateMasterInterface $sourceObject */
    if($sourceObject = $entityManagerEvent->getSourceObject())
    {
      if(AustralTools::usedImplements(get_class($sourceObject), TranslateMasterInterface::class))
      {
        $entityManagerEvent->getObject()->removeAllTranslates();
        foreach($sourceObject->getTranslates() as $translate)
        {
          $duplicateTranslate = $entityManagerEvent->getEntityManager()->duplicate($translate);
          $entityManagerEvent->getObject()->addTranslateByLanguage($duplicateTranslate);
          $entityManagerEvent->getEntityManager()->update($duplicateTranslate, false);
        }
      }
    }
  }

}