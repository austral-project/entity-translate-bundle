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

use Austral\Contracts\Translate\TranslateChildInterface;
use Austral\Contracts\Translate\TranslateMasterInterface;
use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\PropertyAccess\FieldAccessor;
use Austral\ToolsBundle\AustralTools;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Austral Translate Entity Trait Master.
 *
 * The class using this trait must initialise $translates (new ArrayCollection()) in its constructor.
 *
 * @template T of object
 *
 * @phpstan-require-implements TranslateMasterInterface
 *
 * @phpstan-require-extends Entity
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTranslateMasterTrait
{
  /**
   * @var Collection<int, TranslateChildInterface<T>>
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
   * @var TranslateChildInterface<T>|null
   */
  protected ?TranslateChildInterface $translateCurrent = null;

  /**
   * @var array<string, TranslateChildInterface<T>>
   */
  protected array $translatesByLanguage = [];

  /**
   * @return TranslateChildInterface<T>
   */
  abstract public function createNewTranslateByLanguage(): TranslateChildInterface;

  /**
   * Reads the master first, then the current translation. Reading never creates a translation.
   *
   * @param string $name
   *
   * @return mixed
   *
   * @throws \Exception
   */
  public function __get(string $name): mixed
  {
    if (FieldAccessor::isReadable($this, $name))
    {
      return FieldAccessor::getValue($this, $name);
    }
    $translate = $this->getTranslateCurrent();

    return $translate ? $this->getTranslateValueByKey($translate, $name) : null;
  }

  /**
   * Answers from the translation class when there is no current translation, so nothing is created.
   *
   * @param string $name
   *
   * @return bool
   *
   * @throws \Exception
   */
  public function __isset(string $name): bool
  {
    if (FieldAccessor::isReadable($this, $name))
    {
      return true;
    }

    return FieldAccessor::isReadable($this->getTranslateCurrent() ?? $this->getTranslateClassname(), $name);
  }

  /**
   * Writes the master first, then the current translation, created if needed.
   *
   * @param string $name
   * @param mixed  $value
   *
   * @return void
   *
   * @throws \Exception
   */
  public function __set(string $name, mixed $value)
  {
    if (FieldAccessor::isWritable($this, $name))
    {
      FieldAccessor::setValue($this, $name, $value);

      return;
    }
    $translate = $this->getTranslateCurrent();
    if (!FieldAccessor::isWritable($translate ?? $this->getTranslateClassname(), $name))
    {
      throw new \LogicException(sprintf('The field "%s" is writable neither on "%s" nor on its translation.', $name, static::class));
    }
    $this->setTranslateValueByKey($translate ?? $this->createNewTranslateByLanguage(), $name, $value);
  }

  /**
   * Add translates
   *
   * @param TranslateChildInterface<T> $translate
   *
   * @return $this
   */
  public function addTranslates(TranslateChildInterface $translate): self
  {
    if (!$this->translates->contains($translate))
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
   * @param TranslateChildInterface<T> $translate
   *
   * @return $this
   */
  public function removeTranslates(TranslateChildInterface $translate): self
  {
    if ($this->translates->contains($translate))
    {
      $this->translates->removeElement($translate);
    }

    return $this;
  }

  /**
   * Get translates
   *
   * @return Collection<int, TranslateChildInterface<T>>
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
   * @return array<int, string>
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
   * @param TranslateChildInterface<T> $translate
   * @param string                     $fieldKey
   *
   * @return mixed null when the translation has no such field
   */
  public function getTranslateValueByKey(TranslateChildInterface $translate, string $fieldKey): mixed
  {
    return FieldAccessor::isReadable($translate, $fieldKey) ? FieldAccessor::getValue($translate, $fieldKey) : null;
  }

  /**
   * @param TranslateChildInterface<T> $translate
   * @param string                     $fieldKey
   * @param mixed                      $value
   *
   * @return $this
   *
   * @throws \Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException when the translation has no such field
   */
  public function setTranslateValueByKey(TranslateChildInterface $translate, string $fieldKey, mixed $value = null): self
  {
    FieldAccessor::setValue($translate, $fieldKey, $value);

    return $this;
  }

  /**
   * @return array<string, TranslateChildInterface<T>>
   */
  public function getTranslatesByLanguage(): array
  {
    if (!$this->translatesByLanguage)
    {
      foreach ($this->getTranslates() as $translate)
      {
        $this->translatesByLanguage[$translate->getLanguage()] = $translate;
      }
    }
    ksort($this->translatesByLanguage);

    return $this->translatesByLanguage;
  }

  /**
   * @return TranslateChildInterface<T>|null
   */
  public function getTranslateReferent(): ?TranslateChildInterface
  {
    $referentTranslate = AustralTools::first($this->getTranslatesByLanguage());
    foreach ($this->getTranslatesByLanguage() as $translate)
    {
      if ($translate->getIsReferent())
      {
        $referentTranslate = $translate;
      }
    }

    return $referentTranslate;
  }

  /**
   * @param string $langue
   *
   * @return TranslateChildInterface<T>|null
   */
  public function getTranslateByLanguage(string $langue): ?TranslateChildInterface
  {
    return AustralTools::valueByKey($this->getTranslatesByLanguage(), $langue);
  }

  /**
   * @return TranslateChildInterface<T>|null
   *
   * @throws \Exception
   */
  public function getTranslateCurrent(): ?TranslateChildInterface
  {
    if (!$this->translateCurrent)
    {
      if ($this->languageCurrent)
      {
        $this->translateCurrent = $this->getTranslateByLanguage($this->languageCurrent);
      }

      if ($translateReferent = $this->getTranslateReferent())
      {
        if (!$this->translateCurrent && $translateReferent->getLanguage() != $this->languageCurrent)
        {
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
   * @param TranslateChildInterface<T> $child
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
   * @param TranslateChildInterface<T> $child
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
