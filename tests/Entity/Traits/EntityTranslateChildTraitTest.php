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

namespace Austral\EntityTranslateBundle\Tests\Entity\Traits;

use Austral\EntityTranslateBundle\Tests\Fixtures\Page;
use Austral\EntityTranslateBundle\Tests\Fixtures\PageTranslate;
use PHPUnit\Framework\TestCase;

final class EntityTranslateChildTraitTest extends TestCase
{
  public function testDefaults(): void
  {
    $translate = new PageTranslate();

    self::assertNull($translate->getLanguage());
    self::assertFalse($translate->getIsReferent());
  }

  public function testAccessors(): void
  {
    $page = new Page();
    $translate = new PageTranslate();

    $translate->setMaster($page)->setLanguage('fr')->setIsReferent(true);

    self::assertSame($page, $translate->getMaster());
    self::assertSame('fr', $translate->getLanguage());
    self::assertTrue($translate->getIsReferent());
  }
}
