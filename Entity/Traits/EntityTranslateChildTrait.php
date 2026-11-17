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

use Austral\EntityBundle\Entity\Entity;
use Austral\Contracts\Translate\TranslateChildInterface;
use Austral\Contracts\Translate\TranslateMasterInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Austral Translate Entity Trait Child.
 *
 * @phpstan-require-implements TranslateChildInterface
 * @phpstan-require-extends Entity
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTranslateChildTrait
{

  /**
   * @var TranslateMasterInterface
   */
  protected TranslateMasterInterface $master;

  /**
   * @var string|null
   */
  #[ORM\Column(name: "language", type: "string", length: 4, nullable: false)]
  protected ?string $language;

  /**
   * @var boolean
   * @ORM\Column(name="is_referent", type="boolean", nullable=true)
   */
  #[ORM\Column(name: "is_referent", type: "boolean", options: ["default" => false])]
  protected bool $isReferent = false;

  /**
   * Set master
   *
   * @param TranslateMasterInterface $master
   *
   * @return $this
   */
  public function setMaster(TranslateMasterInterface $master): self
  {
    $this->master = $master;
    return $this;
  }

  /**
   * Get master
   * @return TranslateMasterInterface
   */
  public function getMaster(): TranslateMasterInterface
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
   * @return $this
   */
  public function setLanguage(?string $language): self
  {
    $this->language = $language;
    return $this;
  }

  /**
   * Set isReferent
   *
   * @param bool $isReferent
   *
   * @return $this
   */
  public function setIsReferent(bool $isReferent): self
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
