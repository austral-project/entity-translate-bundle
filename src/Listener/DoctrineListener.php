<?php

/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityTranslateBundle\Listener;

use Austral\Contracts\Translate\TranslateMasterInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Austral Doctrine Listener.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class DoctrineListener
{
  /**
   * @var RequestStack|null
   */
  protected ?RequestStack $requestStack = null;

  /**
   * @param RequestStack $requestStack
   */
  public function setRequestStack(RequestStack $requestStack): void
  {
    $this->requestStack = $requestStack;
  }

  /**
   * The request is read when an entity is loaded, not when the listener is created:
   * the current request can change during the life of the service (sub-requests, long-running workers).
   *
   * @return string|null
   */
  public function getCurrentLanguage(): ?string
  {
    if (!$request = $this->requestStack?->getCurrentRequest())
    {
      return null;
    }
    foreach ([$request->attributes->get('language'), $request->attributes->get('_locale'), $request->getLocale()] as $language)
    {
      if (is_string($language) && '' !== $language)
      {
        return $language;
      }
    }

    return null;
  }

  /**
   * @param PostLoadEventArgs $args
   */
  public function postLoad(PostLoadEventArgs $args): void
  {
    $object = $args->getObject();
    if ($object instanceof TranslateMasterInterface)
    {
      if ($lang = $this->getCurrentLanguage())
      {
        $object->setCurrentLanguage($lang);
      }
    }
  }
}
