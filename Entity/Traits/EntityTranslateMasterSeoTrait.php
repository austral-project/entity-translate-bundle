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

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntitySeoBundle\Entity\Interfaces\EntitySeoInterface;
use Austral\EntitySeoBundle\Entity\Traits\EntitySeoTrait;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateChildInterface;
use Exception;

/**
 * Austral Translate Entity Seo To Master Translate Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTranslateMasterSeoTrait
{

  /**
   * @return EntitySeoInterface|EntityInterface|EntityTranslateChildInterface|null
   * @throws Exception
   */
  private function getTranslateCurrentSeo(): ?EntitySeoInterface
  {
    return $this->getTranslateCurrent();
  }

  /**
   * Get RefH1
   * @return string|null
   * @throws Exception
   */
  public function getRefH1(): ?string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getRefH1() : null;
  }

  /**
   * @param string|null $refH1
   *
   * @return EntitySeoInterface|EntityTranslateMasterSeoTrait
   * @throws Exception
   */
  public function setRefH1(?string $refH1): EntitySeoInterface
  {
    if($this->getTranslateCurrentSeo())
    {
      $this->getTranslateCurrentSeo()->setRefH1($refH1);
    }
    return $this;
  }

  /**
   * @return string|null
   * @throws Exception
   */
  public function getRefH1OrDefault(): ?string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getRefH1OrDefault() : null;
  }

  /**
   * Get refTitle
   * @return string|null
   * @throws Exception
   */
  public function getRefTitle(): ?string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getRefTitle() : null;
  }

  /**
   * @param string|null $refTitle
   *
   * @return EntitySeoInterface|EntityTranslateMasterSeoTrait
   * @throws Exception
   */
  public function setRefTitle(?string $refTitle): EntitySeoInterface
  {
    if($this->getTranslateCurrentSeo())
    {
      $this->getTranslateCurrentSeo()->setRefTitle($refTitle);
    }
    return $this;
  }

  /**
   * Get refUrl
   * @return string|null
   * @throws Exception
   */
  public function getRefUrl(): ?string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getRefUrl() : null;
  }

  /**
   * @param string $refUrl
   *
   * @return EntitySeoInterface|EntityTranslateMasterSeoTrait
   * @throws Exception
   */
  public function setRefUrl($refUrl): EntitySeoInterface
  {
    if($this->getTranslateCurrentSeo())
    {
      $this->getTranslateCurrentSeo()->setRefUrl($refUrl);
    }
    return $this;
  }

  /**
   * Get refUrlLast
   * @return string|null
   * @throws Exception
   */
  public function getRefUrlLast(): ?string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getRefUrlLast() : null;
  }

  /**
   * @param string $refUrlLast
   *
   * @return EntitySeoInterface|EntityTranslateMasterSeoTrait
   * @throws Exception
   */
  public function setRefUrlLast($refUrlLast): EntitySeoInterface
  {
    if($this->getTranslateCurrentSeo())
    {
      $this->getTranslateCurrentSeo()->setRefUrlLast($refUrlLast);
    }
    return $this;
  }

  /**
   * Get refDescription
   * @return string|null
   * @throws Exception
   */
  public function getRefDescription(): ?string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getRefDescription() : null;
  }

  /**
   * @param string $refDescription
   *
   * @return EntitySeoInterface|EntityTranslateMasterSeoTrait
   * @throws Exception
   */
  public function setRefDescription($refDescription): EntitySeoInterface
  {
    if($this->getTranslateCurrentSeo())
    {
      $this->getTranslateCurrentSeo()->setRefDescription($refDescription);
    }
    return $this;
  }

  /**
   * Get canonical
   * @return string|null
   * @throws Exception
   */
  public function getCanonical(): ?string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getCanonical() : null;
  }

  /**
   * @param string $canonical
   *
   * @return EntitySeoInterface|EntityTranslateMasterSeoTrait
   * @throws Exception
   */
  public function setCanonical($canonical): EntitySeoInterface
  {
    if($this->getTranslateCurrentSeo())
    {
      $this->getTranslateCurrentSeo()->setCanonical($canonical);
    }
    return $this;
  }

  /**
   * @return string
   * @throws Exception
   */
  public function getBaseUrl(): string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getBaseUrl() : "";
  }

  /**
   * @return bool
   */
  public function getRefUrlLastEnabled(): bool
  {
    return true;
  }

  /**
   * @return string|null
   * @throws Exception
   */
  public function getBodyClass(): ?string
  {
    return $this->getTranslateCurrentSeo() ? $this->getTranslateCurrentSeo()->getBodyClass() : "";
  }

  /**
   * @param string|null $bodyClass
   *
   * @return EntitySeoInterface|EntityTranslateMasterSeoTrait
   * @throws Exception
   */
  public function setBodyClass(?string $bodyClass): EntitySeoInterface
  {
    if($this->getTranslateCurrentSeo())
    {
      $this->getTranslateCurrentSeo()->setBodyClass($bodyClass);
    }
    return $this;
  }

  /**
   * @return EntitySeoInterface
   */
  public function getHomepage(): EntitySeoInterface
  {
    if(method_exists($this, "getIsHomepage") && $this->getIsHomepage())
    {
      return $this;
    }
    return $this->getPageParent()->getHomepage();
  }

}
