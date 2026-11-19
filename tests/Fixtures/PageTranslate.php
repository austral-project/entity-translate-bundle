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
use Austral\EntityBundle\Entity\Entity;
use Austral\EntityTranslateBundle\Entity\Traits\EntityTranslateChildTrait;

/**
 * Translation of the Page test entity.
 *
 * @extends Entity<object>
 *
 * @implements TranslateChildInterface<object>
 */
final class PageTranslate extends Entity implements TranslateChildInterface
{
  /** @use EntityTranslateChildTrait<object> */
  use EntityTranslateChildTrait;

  /**
   * @var string|null
   */
  protected ?string $title = null;

  /**
   * @return string|null
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * @param string|null $title
   *
   * @return $this
   */
  public function setTitle(?string $title): self
  {
    $this->title = $title;

    return $this;
  }
}
