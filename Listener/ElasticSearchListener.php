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


use Austral\ElasticSearchBundle\Event\ElasticSearchSelectObjectsEvent;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\EntityBundle\Mapping\EntityMapping;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\EntityTranslateBundle\Mapping\EntityTranslateMapping;
use Austral\ElasticSearchBundle\Model\ObjectToHydrate;

/**
 * Austral ElasticSearch Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class ElasticSearchListener
{

  /**
   * @var Mapping
   */
  protected Mapping $mapping;

  /**
   * @param Mapping $mapping
   */
  public function __construct(Mapping $mapping)
  {
    $this->mapping = $mapping;
  }

  public function queryBuilder(ElasticSearchSelectObjectsEvent $elasticSearchSelectObjectsEvent)
  {
    /** @var EntityMapping $entityMapping */
    $entityMapping = $this->mapping->getEntityMapping($elasticSearchSelectObjectsEvent->getEntityClass());
    if($entityMapping && $entityMapping->getEntityClassMapping(EntityTranslateMapping::class))
    {
      $elasticSearchSelectObjectsEvent->getQueryBuilder()->leftJoin("root.translates", "translates")->addSelect("translates");
    }
  }

  /**
   * @param ElasticSearchSelectObjectsEvent $elasticSearchSelectObjectsEvent
   *
   * @return void
   */
  public function objects(ElasticSearchSelectObjectsEvent $elasticSearchSelectObjectsEvent)
  {
    /** @var EntityMapping $entityMapping */
    $entityMapping = $this->mapping->getEntityMapping($elasticSearchSelectObjectsEvent->getEntityClass());
    if($entityMapping && $entityMapping->getEntityClassMapping(EntityTranslateMapping::class))
    {
      $objectsToHydrate = array();
      /** @var ObjectToHydrate $objectToHydrate */
      foreach ($elasticSearchSelectObjectsEvent->getObjectsToHydrate() as $objectToHydrate)
      {
        /** @var TranslateMasterInterface|EntityInterface $object */
        $object = $objectToHydrate->getObject();
        foreach ($object->getTranslates() as $translate)
        {
          $objectToHydrateClone = clone $objectToHydrate;
          $objectByLanguage = clone $object;
          $objectByLanguage->setCurrentLanguage($translate->getLanguage());
          $objectToHydrateClone->setObject($object);
          $objectToHydrateClone->setElasticSearchId(sprintf("%s_%s", $objectToHydrate->getElasticSearchId(), $translate->getLanguage()));
          $objectsToHydrate[$objectToHydrateClone->getElasticSearchId()] = $objectToHydrate;
        }
      }
      $elasticSearchSelectObjectsEvent->setObjectsToHydrate($objectsToHydrate);
    }
  }


}
