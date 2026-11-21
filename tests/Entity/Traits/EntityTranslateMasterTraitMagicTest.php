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
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

/**
 * __get, __isset and __set of EntityTranslateMasterTrait: reading never creates a translation.
 */
final class EntityTranslateMasterTraitMagicTest extends TestCase
{
  public function testGetReadsTheMasterFirst(): void
  {
    self::assertSame('home', self::page('Accueil')->__get('keyname'));
  }

  public function testGetReadsTheCurrentTranslation(): void
  {
    self::assertSame('Accueil', self::page('Accueil')->__get('title'));
  }

  public function testGetWithoutTranslationReturnsNullAndCreatesNothing(): void
  {
    $page = new Page();

    self::assertNull($page->__get('title'));
    self::assertNull($page->getTranslateCurrent());
    self::assertCount(0, $page->getTranslates());
  }

  public function testGetOnUnknownFieldReturnsNull(): void
  {
    self::assertNull(self::page('Accueil')->__get('unknown'));
    self::assertNull(new Page()->__get('unknown'));
  }

  public function testIssetOnMasterAndTranslationFields(): void
  {
    $page = self::page('Accueil');

    self::assertTrue($page->__isset('keyname'));
    self::assertTrue($page->__isset('title'));
    self::assertFalse($page->__isset('unknown'));
  }

  public function testIssetWithoutTranslationAnswersFromTheTranslationClassAndCreatesNothing(): void
  {
    $page = new Page();

    self::assertTrue($page->__isset('title'));
    self::assertFalse($page->__isset('unknown'));
    self::assertNull($page->getTranslateCurrent());
    self::assertCount(0, $page->getTranslates());
  }

  public function testMagicPropertySyntaxAsUsedByTwig(): void
  {
    $page = new Page();

    self::assertTrue(isset($page->title));
    self::assertCount(0, $page->getTranslates());
  }

  public function testSetWritesTheMasterFirst(): void
  {
    $page = self::page('Accueil');
    $page->__set('keyname', 'about');

    self::assertSame('about', $page->getKeyname());
    self::assertSame('Accueil', self::currentTitle($page));
  }

  public function testSetWritesTheCurrentTranslation(): void
  {
    $page = self::page('Accueil');
    $page->__set('title', 'À propos');

    self::assertSame('À propos', self::currentTitle($page));
    self::assertCount(1, $page->getTranslates());
  }

  public function testSetWithoutTranslationCreatesIt(): void
  {
    $page = new Page();
    $page->__set('title', 'À propos');

    self::assertCount(1, $page->getTranslates());
    self::assertSame('À propos', self::currentTitle($page));
  }

  public function testSetOnUnknownFieldThrowsWithoutCreatingATranslation(): void
  {
    $page = new Page();

    try
    {
      $page->__set('unknown', 'value');
      self::fail('A LogicException was expected.');
    }
    catch (\LogicException $exception)
    {
      self::assertStringContainsString('"unknown"', $exception->getMessage());
      self::assertCount(0, $page->getTranslates());
    }
  }

  public function testGetTranslateValueByKey(): void
  {
    $page = new Page();
    $translate = self::translate('Accueil');

    self::assertSame('Accueil', $page->getTranslateValueByKey($translate, 'title'));
    self::assertNull($page->getTranslateValueByKey($translate, 'unknown'));
  }

  public function testSetTranslateValueByKey(): void
  {
    $page = new Page();
    $translate = self::translate('Accueil');

    self::assertSame($page, $page->setTranslateValueByKey($translate, 'title', 'À propos'));
    self::assertSame('À propos', $translate->getTitle());

    $this->expectException(NoSuchPropertyException::class);
    $page->setTranslateValueByKey($translate, 'unknown', 'value');
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
