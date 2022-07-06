<?php
/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\EntityTranslateBundle\Entity\Traits;

use Austral\ContentBlockBundle\Entity\Interfaces\ComponentInterface;
use Austral\ContentBlockBundle\Entity\Interfaces\EntityContentBlockInterface;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateChildInterface;
use DateTime;

/**
 * Austral Translate Entity Component To Master Translate Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTranslateMasterComponentsTrait
{

  /**
   * @return EntityTranslateMasterComponentsTrait|EntityContentBlockInterface|EntityTranslateChildInterface
   * @throws \Exception
   */
  private function getTranslateCurrentComponent(): EntityContentBlockInterface
  {
    return $this->getTranslateCurrent();
  }

  /**
   * @return array
   * @throws \Exception
   */
  public function getComponents(): array
  {
    return $this->getTranslateCurrentComponent() ? $this->getTranslateCurrentComponent()->getComponents() : array();
  }

  /**
   * @return array
   * @throws \Exception
   */
  public function getComponentsContainerNames(): array
  {
    return $this->getTranslateCurrentComponent() ? $this->getTranslateCurrentComponent()->getComponents() : array();
  }

  /**
   * @return array
   * @throws \Exception
   */
  public function getComponentsRemoved(): array
  {
    return $this->getTranslateCurrentComponent() ? $this->getTranslateCurrentComponent()->getComponentsRemoved() : array();
  }

  /**
   * @param array $components
   *
   * @return EntityTranslateMasterComponentsTrait|EntityContentBlockInterface
   * @throws \Exception
   */
  public function setComponents(array $components): EntityContentBlockInterface
  {
    if($this->getTranslateCurrentComponent())
    {
      $this->getTranslateCurrentComponent()->setComponents($components);
    }
    return $this;
  }

  /**
   * @param array $componentsTemplate
   *
   * @return EntityTranslateMasterComponentsTrait|EntityContentBlockInterface
   * @throws \Exception
   */
  public function setComponentsTemplate(array $componentsTemplate): EntityContentBlockInterface
  {
    if($this->getTranslateCurrentComponent())
    {
      $this->getTranslateCurrentComponent()->setComponentsTemplate($componentsTemplate);
    }
    return $this;
  }


  /**
   * @param string $containerName
   *
   * @return array
   * @throws \Exception
   */
  public function getComponentsByContainerName(string $containerName): array
  {
    if($this->getTranslateCurrentComponent())
    {
      return $this->getTranslateCurrentComponent()->getComponentsByContainerName($containerName);
    }
    return array();
  }

  /**
   * @param string $containerName
   *
   * @return array
   * @throws \Exception
   */
  public function getComponentsTemplateByContainerName(string $containerName = "master"): array
  {
    if($this->getTranslateCurrentComponent())
    {
      return $this->getTranslateCurrentComponent()->getComponentsTemplateByContainerName($containerName);
    }
    return array();
  }


  /**
   * @return EntityTranslateMasterComponentsTrait|EntityContentBlockInterface
   * @throws \Exception
   */
  public function getComponentsTemplate(): array
  {
    if($this->getTranslateCurrentComponent())
    {
      return $this->getTranslateCurrentComponent()->getComponentsTemplate();
    }
    return array();
  }

  /**
   * Add child
   *
   * @param string $containerName
   * @param ComponentInterface $child
   *
   * @return EntityTranslateMasterComponentsTrait|EntityContentBlockInterface
   * @throws \Exception
   */
  public function addComponents(string $containerName, ComponentInterface $child): EntityContentBlockInterface
  {
    if($this->getTranslateCurrentComponent())
    {
      $this->getTranslateCurrentComponent()->addComponents($containerName, $child);
    }
    return $this;
  }

  /**
   * Remove child
   *
   * @param string $containerName
   * @param ComponentInterface $child
   *
   * @return EntityTranslateMasterComponentsTrait|EntityContentBlockInterface
   * @throws \Exception
   */
  public function removeComponents(string $containerName, ComponentInterface $child):EntityContentBlockInterface
  {
    if($this->getTranslateCurrentComponent())
    {
      $this->getTranslateCurrentComponent()->removeComponents($containerName, $child);
    }
    return $this;
  }

  /**
   * @return DateTime
   * @throws \Exception
   */
  public function getComponentsUpdated(): DateTime
  {
    return $this->getTranslateCurrentComponent() ? $this->getTranslateCurrentComponent()->getComponentsUpdated() : new DateTime();
  }

  /**
   * @param DateTime $componentsUpdated
   *
   * @return $this|EntityContentBlockInterface
   * @throws \Exception
   */
  public function setComponentsUpdated(DateTime $componentsUpdated): EntityContentBlockInterface
  {
    if($this->getTranslateCurrentComponent())
    {
      $this->getTranslateCurrentComponent()->setComponentsUpdated($componentsUpdated);
    }
    return $this;
  }
}
