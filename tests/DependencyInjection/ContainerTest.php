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

namespace Austral\EntityTranslateBundle\Tests\DependencyInjection;

use Austral\EntityTranslateBundle\DependencyInjection\AustralEntityTranslateExtension;
use Austral\EntityTranslateBundle\Listener\DoctrineListener;
use Austral\EntityTranslateBundle\Listener\EntityManagerListener;
use Austral\EntityTranslateBundle\Listener\EntityMappingListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The services are defined explicitly: a missing or misnamed argument only shows up when the container
 * is compiled or the service is created. This test loads the extension, compiles the container and
 * creates every service of the bundle.
 */
final class ContainerTest extends TestCase
{
  /**
   * @return iterable<string, array{class-string}>
   */
  public static function serviceIds(): iterable
  {
    yield DoctrineListener::class => [DoctrineListener::class];
    yield EntityManagerListener::class => [EntityManagerListener::class];
    yield EntityMappingListener::class => [EntityMappingListener::class];
  }

  /**
   * @param class-string $class
   */
  #[DataProvider('serviceIds')]
  public function testEveryServiceCanBeCreated(string $class): void
  {
    self::assertInstanceOf($class, $this->compiledContainer()->get($class));
  }

  public function testTheBundleDefinesNoOtherService(): void
  {
    $ids = [];
    foreach (self::loadExtension()->getDefinitions() as $id => $definition)
    {
      if (str_starts_with($id, 'Austral\\EntityTranslateBundle\\'))
      {
        $ids[] = $id;
      }
    }
    sort($ids);

    self::assertSame([DoctrineListener::class, EntityManagerListener::class, EntityMappingListener::class], $ids);
  }

  /**
   * Listeners are called by the dispatchers, never fetched from the container: nothing is public.
   */
  public function testNothingIsPublic(): void
  {
    $container = self::loadExtension();

    foreach ($container->getDefinitions() as $id => $definition)
    {
      if (str_starts_with($id, 'Austral\\EntityTranslateBundle\\'))
      {
        self::assertFalse($definition->isPublic(), sprintf('The service "%s" must be private.', $id));
      }
    }
    foreach ($container->getAliases() as $id => $alias)
    {
      self::assertFalse(str_starts_with($id, 'austral.entity_translate'), sprintf('The 3.1 alias "%s" must be removed.', $id));
    }
  }

  public function testTheDoctrineListenerReceivesTheRequestStack(): void
  {
    $listener = $this->compiledContainer()->get(DoctrineListener::class);
    self::assertInstanceOf(DoctrineListener::class, $listener);

    $requestStack = new \ReflectionProperty(DoctrineListener::class, 'requestStack')->getValue($listener);

    self::assertInstanceOf(RequestStack::class, $requestStack);
  }

  public function testTheDoctrineListenerListensToPostLoad(): void
  {
    $tags = self::loadExtension()->getDefinition(DoctrineListener::class)->getTag('doctrine.event_listener');

    self::assertSame([['event' => 'postLoad']], $tags);
  }

  public function testTheEntityManagerListenerIsTagged(): void
  {
    $tags = self::loadExtension()->getDefinition(EntityManagerListener::class)->getTag('kernel.event_listener');

    self::assertSame([
      ['event' => 'austral.entity_manager.duplicate', 'method' => 'duplicate', 'priority' => 1000],
      ['event' => 'austral.entity_manager.mapping_association', 'method' => 'mapping'],
    ], $tags);
  }

  public function testTheEntityMappingListenerRunsBeforeAndAfterTheMapping(): void
  {
    $tags = self::loadExtension()->getDefinition(EntityMappingListener::class)->getTag('kernel.event_listener');

    self::assertSame([
      ['event' => 'austral.event.entity.initialise.mapping', 'method' => 'mapping', 'priority' => 2048],
      ['event' => 'austral.event.entity.initialise.mapping', 'method' => 'mappingAfter', 'priority' => -2048],
    ], $tags);
  }

  /**
   * The services that the bundle needs from the application (the request stack) are added as minimal definitions.
   */
  private static function loadExtension(): ContainerBuilder
  {
    $container = new ContainerBuilder();
    $container->setDefinition('request_stack', new Definition(RequestStack::class));
    new AustralEntityTranslateExtension()->load([], $container);

    return $container;
  }

  private function compiledContainer(): ContainerBuilder
  {
    $container = self::loadExtension();
    foreach ($container->getDefinitions() as $id => $definition)
    {
      if (str_starts_with($id, 'Austral\\EntityTranslateBundle\\'))
      {
        $definition->setPublic(true);
      }
    }
    $container->compile();

    return $container;
  }
}
