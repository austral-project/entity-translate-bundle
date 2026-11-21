<?php

/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Austral\EntityTranslateBundle\Tests\Fixtures;

use Austral\Contracts\Translate\TranslateChildInterface;
use Austral\Contracts\Translate\TranslateMasterInterface;
use Austral\EntityBundle\Entity\Entity;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateMasterTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Translated test entity: the title is stored in PageTranslate.
 *
 * @extends Entity<object>
 *
 * @implements TranslateMasterInterface<object>
 */
final class Page extends Entity implements TranslateMasterInterface
{
  /** @use EntityTranslateMasterTrait<object> */
  use EntityTranslateMasterTrait;

  /**
   * @var string
   */
  protected string $keyname = 'home';

  public function __construct()
  {
    parent::__construct();
    $this->translates = new ArrayCollection();
  }

  /**
   * @return string
   */
  public function getKeyname(): string
  {
    return $this->keyname;
  }

  /**
   * @param string $keyname
   *
   * @return $this
   */
  public function setKeyname(string $keyname): self
  {
    $this->keyname = $keyname;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getTranslateClassname(): string
  {
    return PageTranslate::class;
  }

  /**
   * @return TranslateChildInterface<object>
   */
  public function createNewTranslateByLanguage(): TranslateChildInterface
  {
    $translate = new PageTranslate();
    $translate->setLanguage($this->getLanguageCurrent());
    $this->setTranslateCurrent($translate);

    return $translate;
  }
}
