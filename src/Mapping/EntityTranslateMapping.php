<?php

/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityTranslateBundle\Mapping;

use Austral\EntityBundle\Mapping\EntityClassMapping;
use Austral\EntityBundle\Mapping\EntityClassMappingInterface;

/**
 * Austral EntityTranslateMapping.
 *
 * @template T of object
 *
 * @extends EntityClassMapping<T>
 *
 * @implements EntityClassMappingInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
final class EntityTranslateMapping extends EntityClassMapping implements EntityClassMappingInterface
{
  /**
   * @var class-string
   */
  protected string $masterClass;

  /**
   * @var class-string
   */
  protected string $translateClass;

  /**
   * EntityTranslateMapping constructor
   *
   * @param class-string $translateClass
   * @param class-string $masterClass
   */
  public function __construct(string $masterClass, string $translateClass)
  {
    $this->masterClass = $masterClass;
    $this->translateClass = $translateClass;
  }

  /**
   * @return string
   */
  public function getMasterClass(): string
  {
    return $this->masterClass;
  }

  /**
   * @param class-string $masterClass
   *
   * @return $this
   */
  public function setMasterClass(string $masterClass): self
  {
    $this->masterClass = $masterClass;

    return $this;
  }

  /**
   * @return class-string
   */
  public function getTranslateClass(): string
  {
    return $this->translateClass;
  }

  /**
   * @param class-string $translateClass
   *
   * @return $this
   */
  public function setTranslateClass(string $translateClass): self
  {
    $this->translateClass = $translateClass;

    return $this;
  }
}
