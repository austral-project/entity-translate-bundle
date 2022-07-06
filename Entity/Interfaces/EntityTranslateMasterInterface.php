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

use Doctrine\Common\Collections\Collection;

/**
 * Austral Translate Entity Master Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityTranslateMasterInterface
{

  /**
   * @return mixed
   */
  public function getId();

  /**
   * @return EntityTranslateChildInterface|mixed
   */
  public function createNewTranslateByLanguage();

  /**
   * @param string $name
   *
   * @return mixed
   */
  public function __get(string $name);

  /**
   * @param string $name
   * @param $value
   *
   * @return $this
   */
  public function __set(string $name, $value);

  /**
   * Add translates
   *
   * @param EntityTranslateChildInterface $translate
   *
   * @return $this
   */
  public function addTranslates(EntityTranslateChildInterface $translate): EntityTranslateMasterInterface;

  /**
   * Remove translates
   *
   * @param EntityTranslateChildInterface $translate
   *
   * @return $this
   */
  public function removeTranslates(EntityTranslateChildInterface $translate): EntityTranslateMasterInterface;

  /**
   * Get translates
   *
   * @return Collection
   */
  public function getTranslates(): Collection;

  /**
   * Remove All translates
   *
   * @return $this
   */
  public function removeAllTranslates(): EntityTranslateMasterInterface;

  /**
   * @return array
   */
  public function getLanguages(): array;

  /**
   * @return string|null
   */
  public function getLanguageCurrent(): ?string;

  /**
   * @param string|null $value
   *
   * @return $this
   */
  public function setCurrentLanguage(?string $value): EntityTranslateMasterInterface;

  /**
   * @param EntityTranslateChildInterface $translate
   * @param $fieldKey
   *
   * @return mixed
   */
  public function getTranslateValueByKey(EntityTranslateChildInterface $translate, $fieldKey);

  /**
   * @param EntityTranslateChildInterface $translate
   * @param $fieldKey
   * @param null $value
   *
   * @return $this
   */
  public function setTranslateValueByKey(EntityTranslateChildInterface $translate, $fieldKey, $value = null): EntityTranslateMasterInterface;

  /**
   * @return array
   */
  public function getTranslatesByLanguage(): array;

  /**
   * @return EntityTranslateChildInterface|null
   */
  public function getTranslateReferent(): ?EntityTranslateChildInterface;

  /**
   * @param string $langue
   *
   * @return EntityTranslateChildInterface|null
   */
  public function getTranslateByLanguage(string $langue): ?EntityTranslateChildInterface;

  /**
   * @return EntityTranslateChildInterface|null
   */
  public function getTranslateCurrent(): ?EntityTranslateChildInterface;

  /**
   * @param EntityTranslateChildInterface $child
   *
   * @return $this
   */
  public function addTranslateByLanguage(EntityTranslateChildInterface $child): EntityTranslateMasterInterface;

}
