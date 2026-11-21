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

namespace Austral\EntityTranslateBundle\Tests\Mapping\ValueAccessor;

use Austral\EntityTranslateBundle\Mapping\ValueAccessor\TranslatableValueAccessor;
use Austral\EntityTranslateBundle\Tests\Fixtures\Page;
use Austral\EntityTranslateBundle\Tests\Fixtures\PageTranslate;
use PHPUnit\Framework\TestCase;

final class TranslatableValueAccessorTest extends TestCase
{
  public function testGetValueReadsTheMasterFirst(): void
  {
    $page = self::page('Accueil');

    self::assertSame('home', new TranslatableValueAccessor()->getValue($page, 'keyname'));
  }

  public function testGetValueReadsTheCurrentTranslation(): void
  {
    $page = self::page('Accueil');

    self::assertSame('Accueil', new TranslatableValueAccessor()->getValue($page, 'title'));
  }

  public function testGetValueOnAnEntityThatIsNotAMaster(): void
  {
    $accessor = new TranslatableValueAccessor();
    $translate = self::translate('Accueil');

    self::assertSame('Accueil', $accessor->getValue($translate, 'title'));
    self::assertNull($accessor->getValue($translate, 'unknown'));
  }

  public function testSetValueWritesTheMasterWithoutTouchingTheTranslation(): void
  {
    $page = self::page('Accueil');
    new TranslatableValueAccessor()->setValue($page, 'keyname', 'about');

    self::assertSame('about', $page->getKeyname());
    self::assertSame('Accueil', self::currentTitle($page));
  }

  public function testSetValueWritesTheCurrentTranslation(): void
  {
    $page = self::page('Accueil');
    new TranslatableValueAccessor()->setValue($page, 'title', 'À propos');

    self::assertSame('À propos', self::currentTitle($page));
    self::assertSame('home', $page->getKeyname());
  }

  public function testSetValueWithoutCurrentTranslationCreatesIt(): void
  {
    $page = new Page();
    new TranslatableValueAccessor()->setValue($page, 'title', 'À propos');

    self::assertCount(1, $page->getTranslates());
    self::assertSame('À propos', self::currentTitle($page));
  }

  public function testSetValueOnUnknownFieldOfAMasterThrowsWithoutCreatingATranslation(): void
  {
    $page = new Page();

    try
    {
      new TranslatableValueAccessor()->setValue($page, 'unknown', 'value');
      self::fail('A LogicException was expected.');
    }
    catch (\LogicException)
    {
      self::assertCount(0, $page->getTranslates());
    }
  }

  public function testGetValueWithoutCurrentTranslationReturnsNullAndCreatesNothing(): void
  {
    $page = new Page();

    self::assertNull(new TranslatableValueAccessor()->getValue($page, 'title'));
    self::assertNull($page->getTranslateCurrent());
    self::assertCount(0, $page->getTranslates());
  }

  public function testGetValueOnUnknownFieldOfAMasterReturnsNull(): void
  {
    $accessor = new TranslatableValueAccessor();

    self::assertNull($accessor->getValue(self::page('Accueil'), 'unknown'));
    self::assertNull($accessor->getValue(new Page(), 'unknown'));
  }

  public function testSetValueOnUnknownFieldOfAnEntityThatIsNotAMasterDoesNothing(): void
  {
    $translate = self::translate('Accueil');
    new TranslatableValueAccessor()->setValue($translate, 'unknown', 'value');

    self::assertSame('Accueil', $translate->getTitle());
  }

  private static function page(string $title): Page
  {
    $page = new Page();
    $page->setTranslateCurrent(self::translate($title));

    return $page;
  }

  private static function translate(string $title): PageTranslate
  {
    $translate = new PageTranslate();
    $translate->setId('fr-id');
    $translate->setIsCreate(false);
    $translate->setLanguage('fr');
    $translate->setTitle($title);

    return $translate;
  }

  private static function currentTitle(Page $page): ?string
  {
    $translate = $page->getTranslateCurrent();
    self::assertInstanceOf(PageTranslate::class, $translate);

    return $translate->getTitle();
  }
}
