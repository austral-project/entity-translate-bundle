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

use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateChildInterface;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Austral Translate Entity Trait Child.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTranslateChildTrait
{

  /**
   * @var EntityTranslateMasterInterface
   */
  protected EntityTranslateMasterInterface $master;

  /**
   * @var string|null
   * @ORM\Column(name="language", type="string", length=4, nullable=false )
   */
  protected ?string $language;

  /**
   * @var boolean
   * @ORM\Column(name="is_referent", type="boolean", nullable=true)
   */
  protected bool $isReferent = false;

  /**
   * Set master
   *
   * @param EntityTranslateMasterInterface $master
   *
   * @return EntityTranslateChildTrait|EntityTranslateChildInterface
   */
  public function setMaster(EntityTranslateMasterInterface $master): EntityTranslateChildInterface
  {
    $this->master = $master;
    return $this;
  }

  /**
   * Get master
   * @return EntityTranslateChildTrait|EntityTranslateMasterInterface
   */
  public function getMaster(): EntityTranslateMasterInterface
  {
    return $this->master;
  }

  /**
   * Get language
   *
   * @return string|null
   */
  public function getLanguage(): ?string
  {
    return $this->language;
  }

  /**
   * Set string language
   *
   * @param string|null $language
   * @return EntityTranslateChildTrait|EntityTranslateChildInterface
   */
  public function setLanguage(?string $language): EntityTranslateChildInterface
  {
    $this->language = $language;
    return $this;
  }

  /**
   * Set isReferent
   *
   * @param bool $isReferent
   *
   * @return EntityTranslateChildTrait|EntityTranslateChildInterface
   */
  public function setIsReferent(bool $isReferent): EntityTranslateChildInterface
  {
    $this->isReferent = $isReferent;
    return $this;
  }

  /**
   * Get isReferent
   * @return bool
   */
  public function getIsReferent(): bool
  {
    return $this->isReferent;
  }

}
