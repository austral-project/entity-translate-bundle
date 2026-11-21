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

use Austral\EntityBundle\EntityAttributes\EntityAttributes;
use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\Mapping\EntityMapping;
use Austral\EntityTranslateBundle\Attribute\Translate;
use Austral\EntityTranslateBundle\Mapping\EntityTranslateMapping;
use Austral\EntityTranslateBundle\Mapping\ValueAccessor\TranslatableValueAccessor;
use Doctrine\Persistence\Mapping\MappingException;

/**
 * Austral Attribute Listener.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityMappingListener
{
  /**
   * @var array<class-string, array<string, mixed>>
   */
  protected array $attributesWithTranslate = [];

  /**
   * @param EntityMappingEvent<T> $entityMappingEvent
   *
   * @return void
   *
   * @throws MappingException
   * @throws \ReflectionException
   */
  public function mapping(EntityMappingEvent $entityMappingEvent): void
  {
    $entitiesAttributes = $entityMappingEvent->getEntitiesAttributes();
    /**
     * @var EntityAttributes<T> $entityAttribute
     */
    foreach ($entitiesAttributes->all() as $entityAttribute)
    {
      foreach ($entityAttribute->getClassAttributes() as $attribute)
      {
        if ($attribute instanceof Translate)
        {
          $reelMetadata = $entitiesAttributes->getEntityManager()->getDoctrineEntityManager()->getMetadataFactory()->getMetadataFor($attribute->relationClass);
          $attribute->relationClass = $reelMetadata->getName();
          if ($entityAttributeTranslate = $entitiesAttributes->getEntityAttributes($attribute->relationClass))
          {
            $entityAttribute->addClassAttributes($entityAttributeTranslate->getClassAttributes());
            $entityAttribute->addFieldsAttributes($entityAttributeTranslate->getFieldsAttributes());
            if (($entityAttribute->getClassAttributes() || $entityAttribute->getFieldsAttributes()) && !$entityMappingEvent->getMapping()->getEntityMapping($entityAttribute->getClassname()))
            {
              $entityMappingEvent->getMapping()->addEntityMapping($entityAttribute->getClassname(), new EntityMapping($entityAttribute->getClassname(), $entityAttribute->getSlugger()));
            }
            $this->attributesWithTranslate[$entityAttribute->getClassname()] = [
              'fields' => $entityAttributeTranslate->getFieldsAttributes(),
              'class' => $entityAttributeTranslate->getClassAttributes(),
            ];
            $entitiesAttributes->removeEntityAttributes($attribute->relationClass);

            /** @var EntityTranslateMapping<T> $entityTranslateMapping */
            $entityTranslateMapping = new EntityTranslateMapping(
              $entityAttribute->getClassname(),
              $attribute->relationClass
            );
            if (!$entityMapping = $entityMappingEvent->getMapping()->getEntityMapping($entityAttribute->getClassname()))
            {
              $entityMapping = new EntityMapping($entityAttribute->getClassname(), $entityAttribute->getSlugger());
              $entityMappingEvent->getMapping()->addEntityMapping($entityAttribute->getClassname(), $entityMapping);
            }
            $entityMapping->addEntityClassMapping($entityTranslateMapping);
          }
        }
      }
    }
  }

  /**
   * @param EntityMappingEvent<T> $entityMappingEvent
   *
   * @return void
   *
   * @throws \Throwable
   */
  public function mappingAfter(EntityMappingEvent $entityMappingEvent): void
  {
    $valueAccessor = new TranslatableValueAccessor();
    foreach ($this->attributesWithTranslate as $entityClassname => $attributesByType)
    {
      foreach ($attributesByType['fields'] as $fieldname => $attributes)
      {
        $fieldsMapping = $entityMappingEvent->getMapping()->getEntityMapping($entityClassname)?->getAllFieldsMappingClassByFieldname($fieldname);
        $fieldsMapping = is_array($fieldsMapping) ? $fieldsMapping : [];
        foreach ($fieldsMapping as $fieldMapping)
        {
          $fieldMapping->setValueAccessor($valueAccessor);
        }
      }
    }
  }
}
