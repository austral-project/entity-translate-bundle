<?php
/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\EntityTranslateBundle\Entity\Traits;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Interfaces\RobotInterface;
use Austral\EntityBundle\Entity\Interfaces\TranslateChildInterface;

/**
 * Austral Translate Entity Robot To Master Translate Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @deprecated
 */
trait EntityTranslateMasterRobotTrait
{

  /**
   * @return RobotInterface|EntityInterface|TranslateChildInterface|null
   * @throws \Exception
   */
  private function getTranslateCurrentRobot(): ?RobotInterface
  {
    return $this->getTranslateCurrent();
  }

  /**
   * Get status
   * @return string
   * @throws \Exception
   */
  public function getStatus(): string
  {
    return $this->getTranslateCurrentRobot() ? $this->getTranslateCurrentRobot()->getStatus() : "disabled";
  }

  /**
   * Set status
   *
   * @param string $status
   *
   * @return RobotInterface|EntityTranslateMasterRobotTrait
   * @throws \Exception
   */
  public function setStatus(string $status): RobotInterface
  {
    if($this->getTranslateCurrentRobot())
    {
      $this->getTranslateCurrentRobot()->setStatus($status);
    }
    return $this;
  }

  /**
   * @return boolean
   * @throws \Exception
   */
  public function isPublished(): bool
  {
    return $this->getTranslateCurrentRobot() && $this->getTranslateCurrentRobot()->isPublished();
  }


  /**
   * @return bool
   * @throws \Exception
   */
  public function getIsIndex(): bool
  {
    return $this->getTranslateCurrentRobot() && $this->getTranslateCurrentRobot()->getIsIndex();
  }

  /**
   * Set isIndex
   *
   * @param bool $isIndex
   *
   * @return RobotInterface|EntityTranslateMasterRobotTrait
   * @throws \Exception
   */
  public function setIsIndex(bool $isIndex): RobotInterface
  {
    if($this->getTranslateCurrentRobot())
    {
      $this->getTranslateCurrentRobot()->setIsIndex($isIndex);
    }
    return $this;
  }

  /**
   * Get isFollow
   * @return bool
   * @throws \Exception
   */
  public function getIsFollow(): bool
  {
    return $this->getTranslateCurrentRobot() && $this->getTranslateCurrentRobot()->getIsFollow();
  }

  /**
   * Set isFollow
   *
   * @param bool $isFollow
   *
   * @return RobotInterface|EntityTranslateMasterRobotTrait
   * @throws \Exception
   */
  public function setIsFollow(bool $isFollow): RobotInterface
  {
    if($this->getTranslateCurrentRobot())
    {
      $this->getTranslateCurrentRobot()->setIsFollow($isFollow);
    }
    return $this;
  }

  /**
   * Get isSitemap
   * @return bool
   * @throws \Exception
   */
  public function getInSitemap(): bool
  {
    return $this->getTranslateCurrentRobot() && $this->getTranslateCurrentRobot()->getInSitemap();
  }

  /**
   * Set inSitemap
   *
   * @param bool $inSitemap
   *
   * @return RobotInterface|EntityTranslateMasterRobotTrait
   * @throws \Exception
   */
  public function setInSitemap(bool $inSitemap): RobotInterface
  {
    if($this->getTranslateCurrentRobot())
    {
      $this->getTranslateCurrentRobot()->setInSitemap($inSitemap);
    }
    return $this;
  }
  
  
}
