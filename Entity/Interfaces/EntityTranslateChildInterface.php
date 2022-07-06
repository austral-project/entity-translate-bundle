<?php
/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityTranslateBundle\Entity\Interfaces;

/**
 * Austral Translate Entity Child Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityTranslateChildInterface
{

  /**
   * @return mixed
   */
  public function getId();

  /**
   * Set master
   *
   * @param EntityTranslateMasterInterface $master
   *
   * @return $this
   */
  public function setMaster(EntityTranslateMasterInterface $master): EntityTranslateChildInterface;

  /**
   * Get master
   * @return EntityTranslateMasterInterface
   */
  public function getMaster(): EntityTranslateMasterInterface;

  /**
   * Get language
   *
   * @return string|null
   */
  public function getLanguage(): ?string;

  /**
   * Set language
   *
   * @param string|null $language
   * @return $this
   */
  public function setLanguage(?string $language): EntityTranslateChildInterface;

  /**
   * Set isReferent
   *
   * @param bool $isReferent
   *
   * @return $this
   */
  public function setIsReferent(bool $isReferent): EntityTranslateChildInterface;

  /**
   * Get isReferent
   * @return bool
   */
  public function getIsReferent(): bool;


}
