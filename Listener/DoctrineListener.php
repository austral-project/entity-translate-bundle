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


use Austral\HttpBundle\Services\HttpRequest;
use Austral\Contracts\Translate\TranslateMasterInterface;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Austral Doctrine Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DoctrineListener
{

  /**
   * @var Request|null
   */
  protected ?Request $request = null;

  /**
   * @param RequestStack $request
   */
  public function setRequestStack(RequestStack $request): void
  {
      $this->request = $request->getCurrentRequest();
  }

  /**
   * @return string|null
   */
  public function getCurrentLanguage(): ?string
  {
    if($this->request && (
        ($lang = $this->request->attributes->get("language")) ||
        ($lang = $this->request->attributes->get("_locale")) ||
        ($lang = $this->request->getLocale())))
    {
      return $lang;
    }
    return null;
  }

  /**
   * @param LifecycleEventArgs<TObjectManager> $args
   */
  public function postLoad(LifecycleEventArgs $args): void
  {
    $object = $args->getObject();
    if($object instanceof TranslateMasterInterface)
    {
      if($lang = $this->getCurrentLanguage())
      {
        $object->setCurrentLanguage($lang);
      }
    }
  }

}