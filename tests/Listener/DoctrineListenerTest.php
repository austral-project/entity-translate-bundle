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

namespace Austral\EntityTranslateBundle\Tests\Listener;

use Austral\EntityTranslateBundle\Listener\DoctrineListener;
use Austral\EntityTranslateBundle\Tests\Fixtures\Page;
use Austral\EntityTranslateBundle\Tests\Fixtures\PageTranslate;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class DoctrineListenerTest extends TestCase
{
  public function testNoRequestLeavesTheLanguageUnchanged(): void
  {
    $listener = new DoctrineListener();
    $listener->setRequestStack(new RequestStack());
    $page = new Page();

    $listener->postLoad($this->args($page));

    self::assertNull($listener->getCurrentLanguage());
    self::assertNull($page->getLanguageCurrent());
  }

  public function testLanguageAttributeComesFirst(): void
  {
    $request = new Request(attributes: ['language' => 'en', '_locale' => 'de']);
    $request->setLocale('fr');
    $page = new Page();

    $this->listener($request)->postLoad($this->args($page));

    self::assertSame('en', $page->getLanguageCurrent());
  }

  public function testLocaleAttributeThenRequestLocale(): void
  {
    $withAttribute = new Request(attributes: ['_locale' => 'de']);
    $withLocale = new Request();
    $withLocale->setLocale('fr');

    self::assertSame('de', $this->listener($withAttribute)->getCurrentLanguage());
    self::assertSame('fr', $this->listener($withLocale)->getCurrentLanguage());
  }

  public function testTheCurrentRequestIsReadWhenTheEntityIsLoaded(): void
  {
    $requestStack = new RequestStack();
    $listener = new DoctrineListener();
    $listener->setRequestStack($requestStack);

    $requestStack->push(new Request(attributes: ['language' => 'en']));

    self::assertSame('en', $listener->getCurrentLanguage());
  }

  public function testOtherEntitiesAreIgnored(): void
  {
    $translate = new PageTranslate();

    $this->listener(new Request(attributes: ['language' => 'en']))->postLoad($this->args($translate));

    self::assertNull($translate->getLanguage());
  }

  private function listener(Request $request): DoctrineListener
  {
    $requestStack = new RequestStack();
    $requestStack->push($request);
    $listener = new DoctrineListener();
    $listener->setRequestStack($requestStack);

    return $listener;
  }

  private function args(object $object): PostLoadEventArgs
  {
    return new PostLoadEventArgs($object, $this->createStub(EntityManagerInterface::class));
  }
}
