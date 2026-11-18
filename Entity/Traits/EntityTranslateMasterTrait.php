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

use Austral\EntityBundle\Entity\Entity;
use Austral\Contracts\Translate\TranslateMasterInterface;
use Austral\ToolsBundle\AustralTools;
use Austral\Contracts\Translate\TranslateChildInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Austral Translate Entity Trait Master.
 *
 * @phpstan-require-implements TranslateMasterInterface
 * @phpstan-require-extends Entity
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTranslateMasterTrait
{
  /**
   * @var Collection<int, TranslateChildInterface>
   */
  protected Collection $translates;

  /**
   * @var string|null
   */
  protected ?string $languageCurrent = null;

  /**
   * @var string|null
   */
  protected ?string $languageSelectForm = null;

  /**
   * @var TranslateChildInterface|null
   */
  protected ?TranslateChildInterface $translateCurrent = null;

  /**
   * @var array<string, TranslateChildInterface>
   */
  protected array $translatesByLanguage = [];

  /**
   * @return TranslateChildInterface
   */
  abstract public function createNewTranslateByLanguage(): TranslateChildInterface;

  /** @return TranslateMasterInterface */
  private function asMaster(): TranslateMasterInterface
  {
    /** @var TranslateMasterInterface $this */
    return $this;
  }

  /**
   * @param string $name
   *
   * @return mixed
   * @throws \Exception
   */
  public function __get(string $name): mixed
  {
    $fieldKey = $name;
    $getter = AustralTools::createGetterFunction($fieldKey);
    if(method_exists($this, $fieldKey) || method_exists($this, $getter))
    {
      return method_exists($this, $fieldKey) ? $this->$fieldKey() : $this->$getter();
    }
    if(!$translate = $this->getTranslateCurrent())
    {
      $translate = $this->createNewTranslateByLanguage();
    }
    return $this->getTranslateValueByKey($translate, $fieldKey);
  }

  /**
   * @param string $name
   *
   * @return mixed
   * @throws \Exception
   */
  public function __isset(string $name): bool
  {
    $fieldKey = $name;
    $getter = AustralTools::createGetterFunction($fieldKey);
    $return = method_exists($this, $fieldKey) || method_exists($this, $getter);
    if(!$return)
    {
      if(!$translate = $this->getTranslateCurrent())
      {
        $translate = $this->createNewTranslateByLanguage();
      }
      $return = method_exists($translate, $fieldKey) || method_exists($translate, $getter);
    }
    return $return;
  }

  /**
   * @param string $name
   * @param $value
   *
   * @return void
   * @throws \Exception
   */
  public function __set(string $name, $value)
  {
    $fieldKey = $name;
    $setter = AustralTools::createSetterFunction($fieldKey);
    if(method_exists($this, $fieldKey) || method_exists($this, $setter))
    {
      return method_exists($this, $fieldKey) ? $this->$fieldKey($value) : $this->$setter($value);
    }
    if(!$translate = $this->getTranslateCurrent())
    {
      $translate = $this->createNewTranslateByLanguage();
    }
    $this->setTranslateValueByKey($translate, $fieldKey, $value);
  }

  /**
   * Add translates
   *
   * @param TranslateChildInterface $translate
   *
   * @return $this
   */
  public function addTranslates(TranslateChildInterface $translate): self
  {
    if(!$this->translates->contains($translate))
    {
      $this->translates->add($translate);
      $translate->setMaster($this->asMaster());
    }
    else
    {
      $this->translates->set($this->translates->indexOf($translate), $translate);
    }
    return $this;
  }

  /**
   * Remove translates
   *
   * @param TranslateChildInterface $translate
   *
   * @return $this
   */
  public function removeTranslates(TranslateChildInterface $translate): self
  {
    if($this->translates->contains($translate))
    {
      $this->translates->removeElement($translate);
    }
    return $this;
  }

  /**
   * Get translates
   *
   * @return Collection
   */
  public function getTranslates(): Collection
  {
    return $this->translates;
  }

  /**
   * @return $this
   */
  public function removeAllTranslates(): self
  {
    $this->translates = new ArrayCollection();
    $this->translatesByLanguage = [];
    $this->translateCurrent = null;
    return $this;
  }

  /**
   * @return array
   */
  public function getLanguages(): array
  {
    return array_keys($this->getTranslatesByLanguage());
  }

  /**
   * @return string|null
   */
  public function getLanguageCurrent(): ?string
  {
    return $this->languageCurrent;
  }

  /**
   * @param string|null $value
   *
   * @return $this
   */
  public function setCurrentLanguage(?string $value): self
  {
    $this->languageCurrent = $value;
    $this->translateCurrent = null;
    return $this;
  }

  /**
   * @param TranslateChildInterface $translate
   * @param string $fieldKey
   *
   * @return mixed
   */
  public function getTranslateValueByKey(TranslateChildInterface $translate, string $fieldKey): mixed
  {
    $getter = AustralTools::createGetterFunction($fieldKey);
    return method_exists($translate, $fieldKey) ? $translate->$fieldKey() : $translate->$getter();
  }

  /**
   * @param TranslateChildInterface $translate
   * @param $fieldKey
   * @param null $value
   *
   * @return $this
   */
  public function setTranslateValueByKey(TranslateChildInterface $translate, $fieldKey, $value = null): self
  {
    $setter = AustralTools::createSetterFunction($fieldKey);
    $translate->$setter($value);
    return $this;
  }

  /**
   * @return array
   */
  public function getTranslatesByLanguage(): array
  {
    if(!$this->translatesByLanguage && $this->getTranslates())
    {
      foreach($this->getTranslates() as $translate)
      {
        $this->translatesByLanguage[$translate->getLanguage()] = $translate;
      }
    }
    ksort($this->translatesByLanguage);
    return $this->translatesByLanguage;
  }

  /**
   * @return TranslateChildInterface|null
   */
  public function getTranslateReferent(): ?TranslateChildInterface
  {
    $referentTranslate = AustralTools::first($this->getTranslatesByLanguage());
    foreach($this->getTranslatesByLanguage() as $translate)
    {
      if($translate->getIsReferent())
      {
        $referentTranslate = $translate;
      }
    }
    return $referentTranslate;
  }

  /**
   * @param string $langue
   *
   * @return TranslateChildInterface|null
   */
  public function getTranslateByLanguage(string $langue): ?TranslateChildInterface
  {
    return AustralTools::valueByKey($this->getTranslatesByLanguage(), $langue);
  }

  /**
   * @return TranslateChildInterface|null
   * @throws \Exception
   */
  public function getTranslateCurrent(): ?TranslateChildInterface
  {
    if(!$this->translateCurrent)
    {
      if($this->languageCurrent)
      {
        $this->translateCurrent = $this->getTranslateByLanguage($this->languageCurrent);
      }

      if($translateReferent = $this->getTranslateReferent())
      {
        if(!$this->translateCurrent && $translateReferent->getLanguage() != $this->languageCurrent)
        {
          /** @var Entity|TranslateChildInterface $translateDuplicate */
          $translateDuplicate = clone $translateReferent;
          $translateDuplicate->setId(Uuid::uuid4()->toString());
          $translateDuplicate->setLanguage($this->languageCurrent);
          $translateDuplicate->setIsCreate(true);
          $this->translateCurrent = $translateDuplicate;
        }
      }
    }
    return $this->translateCurrent;
  }

  /**
   * @param TranslateChildInterface $child
   *
   * @return $this
   */
  public function setTranslateCurrent(TranslateChildInterface $child): self
  {
    $this->addTranslateByLanguage($child);
    $this->translateCurrent = $child;
    return $this;
  }

  /**
   * @param TranslateChildInterface $child
   *
   * @return $this
   */
  public function addTranslateByLanguage(TranslateChildInterface $child): self
  {
    $this->addTranslates($child);
    $this->translatesByLanguage[$child->getLanguage()] = $child;
    return $this;
  }

}
