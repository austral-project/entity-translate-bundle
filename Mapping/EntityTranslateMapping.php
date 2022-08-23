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

/**
 * Austral EntityTranslateMapping.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
final Class EntityTranslateMapping extends EntityClassMapping
{

  /**
   * @var string
   */
  protected string $translateClass;

  /**
   * @var string
   */
  protected string $masterClass;

  /**
   * Constructor.
   */
  public function __construct()
  {
  }

  /**
   * @return string
   */
  public function getTranslateClass(): string
  {
    return $this->translateClass;
  }

  /**
   * @param string $translateClass
   *
   * @return $this
   */
  public function setTranslateClass(string $translateClass): EntityTranslateMapping
  {
    $this->translateClass = $translateClass;
    return $this;
  }

  /**
   * @return string
   */
  public function getMasterClass(): string
  {
    return $this->masterClass;
  }

  /**
   * @param string $masterClass
   *
   * @return $this
   */
  public function setMasterClass(string $masterClass): EntityTranslateMapping
  {
    $this->masterClass = $masterClass;
    return $this;
  }

}
