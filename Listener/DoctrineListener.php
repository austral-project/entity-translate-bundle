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


use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
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
   * DoctrineListener constructor.
   */
  public function __construct()
  {
      $parts = explode('\\', $this->getNamespace());
      $this->name = end($parts);
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
    if($object instanceof EntityTranslateMasterInterface)
    {
      if($this->request && (
          ($lang = $this->request->attributes->get("language")) ||
          ($lang = $this->request->attributes->get("_locale")) ||
          ($lang = $this->request->getLocale())))
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