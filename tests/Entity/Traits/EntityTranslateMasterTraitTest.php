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

final class EntityTranslateMasterTraitTest extends TestCase
{
  public function testNoTranslationByDefault(): void
  {
    $page = new Page();

    self::assertCount(0, $page->getTranslates());
    self::assertSame([], $page->getLanguages());
    self::assertNull($page->getTranslateReferent());
    self::assertNull($page->getTranslateCurrent());
  }

  public function testAddTranslateByLanguageLinksTheMasterAndIndexesByLanguage(): void
  {
    $page = new Page();
    $fr = self::translate('fr', 'Accueil');
    $en = self::translate('en', 'Home');

    $page->addTranslateByLanguage($fr)->addTranslateByLanguage($en);

    self::assertSame($page, $fr->getMaster());
    self::assertSame($page, $en->getMaster());
    self::assertCount(2, $page->getTranslates());
    self::assertSame(['en', 'fr'], $page->getLanguages());
    self::assertSame($en, $page->getTranslateByLanguage('en'));
    self::assertNull($page->getTranslateByLanguage('de'));
  }

  public function testAddingTheSameTranslateTwiceKeepsOneEntry(): void
  {
    $page = new Page();
    $fr = self::translate('fr', 'Accueil');

    $page->addTranslates($fr)->addTranslates($fr);

    self::assertCount(1, $page->getTranslates());
  }

  public function testTranslatesAreIndexedFromTheCollection(): void
  {
    $page = new Page();
    $page->addTranslates(self::translate('fr', 'Accueil'));

    self::assertSame(['fr'], $page->getLanguages());
  }

  public function testCurrentTranslateFollowsTheCurrentLanguage(): void
  {
    $page = self::page();

    $page->setCurrentLanguage('en');
    self::assertSame('en', $page->getLanguageCurrent());
    self::assertSame('en', $page->getTranslateCurrent()?->getLanguage());
    self::assertSame('Home', $page->__get('title'));

    $page->setCurrentLanguage('fr');
    self::assertSame('Accueil', $page->__get('title'));
  }

  public function testReferentIsTheTranslateFlaggedAsReferent(): void
  {
    $page = self::page();

    self::assertSame('fr', $page->getTranslateReferent()?->getLanguage());
  }

  public function testReferentDefaultsToTheFirstLanguage(): void
  {
    $page = new Page();
    $page->addTranslateByLanguage(self::translate('fr', 'Accueil'));
    $page->addTranslateByLanguage(self::translate('en', 'Home'));

    self::assertSame('en', $page->getTranslateReferent()?->getLanguage());
  }

  public function testMissingLanguageIsCreatedFromTheReferent(): void
  {
    $page = self::page();
    $referent = $page->getTranslateByLanguage('fr');

    $page->setCurrentLanguage('de');
    $current = $page->getTranslateCurrent();

    self::assertInstanceOf(PageTranslate::class, $current);
    self::assertNotSame($referent, $current);
    self::assertSame('de', $current->getLanguage());
    self::assertSame('Accueil', $current->getTitle());
    self::assertTrue($current->getIsCreate());
    self::assertNotSame($referent?->getId(), $current->getId());
    self::assertSame('fr', $referent?->getLanguage());
    self::assertSame(['en', 'fr'], $page->getLanguages());
  }

  public function testMagicAccessorsReadAndWriteTheMasterFirst(): void
  {
    $page = self::page();
    $page->setCurrentLanguage('en');

    $page->__set('keyname', 'homepage');
    $page->__set('title', 'Homepage');

    self::assertSame('homepage', $page->getKeyname());
    self::assertSame('homepage', $page->__get('keyname'));
    self::assertSame('Homepage', self::titleOf($page, 'en'));
    self::assertSame('Accueil', self::titleOf($page, 'fr'));
  }

  public function testMagicSetterCreatesTheTranslateWhenThereIsNone(): void
  {
    $page = new Page();
    $page->setCurrentLanguage('fr');

    $page->__set('title', 'Accueil');

    self::assertSame(['fr'], $page->getLanguages());
    self::assertSame('Accueil', self::titleOf($page, 'fr'));
  }

  public function testMagicIsset(): void
  {
    $page = self::page();
    $page->setCurrentLanguage('fr');

    self::assertTrue($page->__isset('keyname'));
    self::assertTrue($page->__isset('title'));
    self::assertFalse($page->__isset('unknown'));
  }

  public function testRemoveTranslates(): void
  {
    $page = self::page();
    $en = $page->getTranslateByLanguage('en');
    self::assertNotNull($en);

    $page->removeTranslates($en);

    self::assertCount(1, $page->getTranslates());
  }

  public function testRemoveAllTranslates(): void
  {
    $page = self::page();
    $page->setCurrentLanguage('en');
    $page->getTranslateCurrent();

    $page->removeAllTranslates();

    self::assertCount(0, $page->getTranslates());
    self::assertSame([], $page->getLanguages());
    self::assertNull($page->getTranslateCurrent());
  }

  private static function page(): Page
  {
    $page = new Page();
    $page->addTranslateByLanguage(self::translate('fr', 'Accueil', true));
    $page->addTranslateByLanguage(self::translate('en', 'Home'));

    return $page;
  }

  private static function titleOf(Page $page, string $language): ?string
  {
    $translate = $page->getTranslateByLanguage($language);
    self::assertInstanceOf(PageTranslate::class, $translate);

    return $translate->getTitle();
  }

  private static function translate(string $language, string $title, bool $isReferent = false): PageTranslate
  {
    $translate = new PageTranslate();
    $translate->setId($language.'-id');
    $translate->setIsCreate(false);
    $translate->setLanguage($language);
    $translate->setTitle($title);
    $translate->setIsReferent($isReferent);

    return $translate;
  }
}
