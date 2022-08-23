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


use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\EntityAnnotation\EntityAnnotations;
use Austral\EntityBundle\Mapping\EntityMapping;
use Austral\EntityBundle\Mapping\FieldMappingInterface;
use Austral\EntityTranslateBundle\Mapping\EntityTranslateMapping;
use Austral\EntityTranslateBundle\Annotation\Translate;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Doctrine\Persistence\Mapping\MappingException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Austral Annotation Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityMappingListener
{

  /**
   * @var array
   */
  protected array $annotationsWithTranslate = array();

  /**
   * @param EntityMappingEvent $entityMappingEvent
   *
   * @return void
   * @throws MappingException
   * @throws \ReflectionException
   */
  public function mapping(EntityMappingEvent $entityMappingEvent)
  {
    $entitiesAnnotations = $entityMappingEvent->getEntitiesAnnotations();
    /**
     * @var string $entityClassname
     * @var EntityAnnotations $entityAnnotation
     */
    foreach($entitiesAnnotations->all() as $entityAnnotation)
    {
      foreach($entityAnnotation->getClassAnnotations() as $annotation)
      {
        if($annotation instanceof Translate)
        {
          if($reelMetadata = $entitiesAnnotations->getEntityManager()->getMetadataFactory()->getMetadataFor($annotation->relationClass))
          {
            $annotation->relationClass = $reelMetadata->getName();
            if($entityAnnotationTranslate = $entitiesAnnotations->getEntityAnnotations($annotation->relationClass))
            {
              $entityAnnotation->addClassAnnotations($entityAnnotationTranslate->getClassAnnotations());
              $entityAnnotation->addFieldsAnnotations($entityAnnotationTranslate->getFieldsAnnotations());
              if(($entityAnnotation->getClassAnnotations() || $entityAnnotation->getFieldsAnnotations()) && !$entityMappingEvent->getMapping()->getEntityMapping($entityAnnotation->getClassname()))
              {
                $entityMappingEvent->getMapping()->addEntityMapping($entityAnnotation->getClassname(), new EntityMapping($entityAnnotation->getClassname(), $entityAnnotation->getSlugger()));
              }
              $this->annotationsWithTranslate[$entityAnnotation->getClassname()] = array(
                "fields" =>  $entityAnnotationTranslate->getFieldsAnnotations(),
                "class" =>  $entityAnnotationTranslate->getClassAnnotations()
              );
              $entitiesAnnotations->removeEntityAnnotations($annotation->relationClass);

              $entityTranslateMapping = new EntityTranslateMapping();
              $entityTranslateMapping->setMasterClass($entityAnnotation->getClassname());
              $entityTranslateMapping->setTranslateClass($annotation->relationClass);
              if(!$entityMapping = $entityMappingEvent->getMapping()->getEntityMapping($entityAnnotation->getClassname()))
              {
                $entityMapping = new EntityMapping($entityAnnotation->getClassname(), $entityAnnotation->getSlugger());
              }
              $entityMapping->addEntityClassMapping($entityTranslateMapping);
            }
          }
        }
      }
    }
  }

  /**
   * @param EntityMappingEvent $entityMappingEvent
   *
   * @return void
   */
  public function mappingAfter(EntityMappingEvent $entityMappingEvent)
  {
    /**
     * @var string $entityClassname
     * @var array $annotationsByType
     */
    foreach ($this->annotationsWithTranslate as $entityClassname => $annotationsByType)
    {
      foreach($annotationsByType["fields"] as $fieldname => $annotations)
      {
        $fieldsMapping = $entityMappingEvent->getMapping()->getEntityMapping($entityClassname)->getAllFieldsMappingClassByFieldname($fieldname);
        /** @var FieldMappingInterface $fieldMapping */
        foreach($fieldsMapping as $fieldMapping)
        {
          $fieldMapping->addGetterObjectValueFonction(function(EntityInterface $object, $fieldname) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            if($propertyAccessor->isReadable($object, $fieldname))
            {
              return $propertyAccessor->getValue($object, $fieldname);
            }
            elseif($object instanceof EntityTranslateMasterInterface)
            {
              if(($currentTranslate = $object->getTranslateCurrent()) && $propertyAccessor->isReadable($currentTranslate, $fieldname))
              {
                return $propertyAccessor->getValue($currentTranslate, $fieldname);
              }
            }
            return null;
          });
          $fieldMapping->addSetterObjectValueFonction(function(EntityInterface $object, $fieldname, $value = null) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            if($propertyAccessor->isWritable($object, $fieldname))
            {
              $propertyAccessor->setValue($object, $fieldname, $value);
            }
            elseif($object instanceof EntityTranslateMasterInterface)
            {
              if(($currentTranslate = $object->getTranslateCurrent()) && $propertyAccessor->isWritable($currentTranslate, $fieldname))
              {
                $propertyAccessor->setValue($currentTranslate, $fieldname, $value);
              }
            }
          });
        }
      }
    }
  }


}
