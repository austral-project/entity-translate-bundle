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
use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\ToolsBundle\AustralTools;
use Austral\EntityBundle\Entity\Interfaces\TranslateChildInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Austral Translate Entity Trait Master.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTranslateMasterTrait
{
  /**
   * @var Collection
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
   * @var array
   */
  protected array $translatesByLanguage = array();

  /**
   * @return mixed
   */
  abstract public function createNewTranslateByLanguage();

  /**
   * @param string $name
   *
   * @return mixed
   * @throws \Exception
   */
  public function __get(string $name)
  {
    $fieldKey = $name;
    if(!$translate = $this->getTranslateCurrent())
    {
      $translate = $this->createNewTranslateByLanguage();
    }
    return $this->getTranslateValueByKey($translate, $fieldKey);
  }

  /**
   * @param string $name
   * @param $value
   *
   * @return TranslateMasterInterface|EntityInterface
   * @throws \Exception
   */
  public function __set(string $name, $value)
  {
    $fieldKey = $name;
    if(!$translate = $this->getTranslateCurrent())
    {
      $translate = $this->createNewTranslateByLanguage();
    }
    return $this->setTranslateValueByKey($translate, $fieldKey, $value);
  }

  /**
   * Add translates
   *
   * @param TranslateChildInterface $translate
   *
   * @return TranslateMasterInterface|EntityInterface
   */
  public function addTranslates(TranslateChildInterface $translate): TranslateMasterInterface
  {
    if(!$this->translates->contains($translate))
    {
      $this->translates->add($translate);
      $translate->setMaster($this);
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
   * @return TranslateMasterInterface|EntityInterface
   */
  public function removeTranslates(TranslateChildInterface $translate): TranslateMasterInterface
  {
    if($this->translates->contains($translate))
    {
      $this->translates->removeElement($translate);
      $translate->setMaster(null);
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
   * @return TranslateMasterInterface|EntityInterface
   */
  public function removeAllTranslates(): TranslateMasterInterface
  {
    $this->translates = new ArrayCollection();
    $this->translatesByLanguage = array();
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
   * @return TranslateMasterInterface|EntityInterface
   */
  public function setCurrentLanguage(?string $value): TranslateMasterInterface
  {
    $this->languageCurrent = $value;
    $this->translateCurrent = null;
    return $this;
  }

  /**
   * @param TranslateChildInterface $translate
   * @param $fieldKey
   *
   * @return mixed
   */
  public function getTranslateValueByKey(TranslateChildInterface $translate, $fieldKey)
  {
    if(method_exists($translate, $fieldKey))
    {
      return $translate->$fieldKey();
    }
    $getter = AustralTools::createGetterFunction($fieldKey);
    return $translate->$getter();
  }

  /**
   * @param TranslateChildInterface $translate
   * @param $fieldKey
   * @param null $value
   *
   * @return TranslateMasterInterface|EntityInterface
   */
  public function setTranslateValueByKey(TranslateChildInterface $translate, $fieldKey, $value = null): TranslateMasterInterface
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
    return AustralTools::getValueByKey($this->getTranslatesByLanguage(), $langue);
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
          $translateDuplicate = clone $translateReferent;
          $translateDuplicate->setId(Uuid::uuid4()->toString());
          $translateDuplicate->setLanguage($this->languageCurrent);
          $translateDuplicate->setIsCreate(true);
          $this->translateCurrent = $translateDuplicate;
          //$this->addTranslates($translateDuplicate);
        }
      }
    }
    return $this->translateCurrent;
  }

  /**
   * @param TranslateChildInterface $child
   *
   * @return TranslateMasterInterface|EntityInterface
   */
  public function setTranslateCurrent(TranslateChildInterface $child): ?TranslateMasterInterface
  {
    $this->addTranslateByLanguage($child);
    $this->translateCurrent = $child;
    return $this;
  }

  /**
   * @param TranslateChildInterface $child
   *
   * @return TranslateMasterInterface|EntityInterface
   */
  public function addTranslateByLanguage(TranslateChildInterface $child): TranslateMasterInterface
  {
    $this->addTranslates($child);
    $this->translatesByLanguage[$child->getLanguage()] = $child;
    return $this;
  }

}
