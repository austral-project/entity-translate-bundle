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
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\ToolsBundle\AustralTools;
use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateChildInterface;
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
   * @var EntityTranslateChildInterface|null
   */
  protected ?EntityTranslateChildInterface $translateCurrent = null;

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
   * @return EntityTranslateMasterInterface|EntityInterface
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
   * @param EntityTranslateChildInterface $translate
   *
   * @return EntityTranslateMasterInterface|EntityInterface
   */
  public function addTranslates(EntityTranslateChildInterface $translate): EntityTranslateMasterInterface
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
   * @param EntityTranslateChildInterface $translate
   *
   * @return EntityTranslateMasterInterface|EntityInterface
   */
  public function removeTranslates(EntityTranslateChildInterface $translate): EntityTranslateMasterInterface
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
   * @return EntityTranslateMasterInterface|EntityInterface
   */
  public function removeAllTranslates(): EntityTranslateMasterInterface
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
   * @return EntityTranslateMasterInterface|EntityInterface
   */
  public function setCurrentLanguage(?string $value): EntityTranslateMasterInterface
  {
    $this->languageCurrent = $value;
    $this->translateCurrent = null;
    return $this;
  }

  /**
   * @param EntityTranslateChildInterface $translate
   * @param $fieldKey
   *
   * @return mixed
   */
  public function getTranslateValueByKey(EntityTranslateChildInterface $translate, $fieldKey)
  {
    if(method_exists($translate, $fieldKey))
    {
      return $translate->$fieldKey();
    }
    $getter = AustralTools::createGetterFunction($fieldKey);
    return $translate->$getter();
  }

  /**
   * @param EntityTranslateChildInterface $translate
   * @param $fieldKey
   * @param null $value
   *
   * @return EntityTranslateMasterInterface|EntityInterface
   */
  public function setTranslateValueByKey(EntityTranslateChildInterface $translate, $fieldKey, $value = null): EntityTranslateMasterInterface
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
   * @return EntityTranslateChildInterface|null
   */
  public function getTranslateReferent(): ?EntityTranslateChildInterface
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
   * @return EntityTranslateChildInterface|null
   */
  public function getTranslateByLanguage(string $langue): ?EntityTranslateChildInterface
  {
    return AustralTools::getValueByKey($this->getTranslatesByLanguage(), $langue);
  }

  /**
   * @return EntityTranslateChildInterface|null
   * @throws \Exception
   */
  public function getTranslateCurrent(): ?EntityTranslateChildInterface
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
   * @param EntityTranslateChildInterface $child
   *
   * @return EntityTranslateMasterInterface|EntityInterface
   */
  public function setTranslateCurrent(EntityTranslateChildInterface $child): ?EntityTranslateMasterInterface
  {
    $this->addTranslateByLanguage($child);
    $this->translateCurrent = $child;
    return $this;
  }

  /**
   * @param EntityTranslateChildInterface $child
   *
   * @return EntityTranslateMasterInterface|EntityInterface
   */
  public function addTranslateByLanguage(EntityTranslateChildInterface $child): EntityTranslateMasterInterface
  {
    $this->addTranslates($child);
    $this->translatesByLanguage[$child->getLanguage()] = $child;
    return $this;
  }

}
