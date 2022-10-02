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


use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\HttpBundle\Services\HttpRequest;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Austral Doctrine Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DoctrineListener implements EventSubscriber
{

  /**
   * @var mixed
   */
  protected $name;

  /**
   * @var HttpRequest|null
   */
  protected ?HttpRequest $httpRequest = null;

  /**
   * DoctrineListener constructor.
   */
  public function __construct(?HttpRequest $httpRequest)
  {
      $parts = explode('\\', $this->getNamespace());
      $this->name = end($parts);
      $this->httpRequest = $httpRequest;
  }

  /**
   * @var Request|null
   */
  protected ?Request $request = null;

  /**
   * @param RequestStack $request
   */
  public function setRequestStack(RequestStack $request)
  {
      $this->request = $request->getCurrentRequest();
  }

  /**
   * @return string|null
   */
  public function getCurrentLanguage(): ?string
  {
    if($this->httpRequest && $this->httpRequest->getLanguage()) {
      return $this->httpRequest->getLanguage();
    }
    elseif($this->request && (
        ($lang = $this->request->attributes->get("language")) ||
        ($lang = $this->request->attributes->get("_locale")) ||
        ($lang = $this->request->getLocale())))
    {
      return $lang;
    }
    return null;
  }

  /**
   * @return string[]
   */
  public function getSubscribedEvents()
  {
      return array(
        'postLoad',
      );
  }

  /**
   * @param EventArgs $args
   */
  public function postLoad(EventArgs $args)
  {
    $ea = $this->getEventAdapter($args);
    $object = $ea->getObject();
    if($object instanceof TranslateMasterInterface)
    {
      if($lang = $this->getCurrentLanguage())
      {
        $object->setCurrentLanguage($lang);
      }
    }
  }

  /**
   * @param EventArgs $args
   *
   * @return EventArgs
   */
  protected function getEventAdapter(EventArgs $args)
  {
    return $args;
  }

  /**
   * @return string
   */
  protected function getNamespace()
  {
    return __NAMESPACE__;
  }
}