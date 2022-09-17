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
use Austral\EntityFileBundle\Entity\Traits\EntityFileCropperTrait;
use Austral\EntityBundle\Entity\Interfaces\TranslateChildInterface;

/**
 * Austral Translate Entity File Cropper To Master Translate Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait EntityTranslateMasterFileCropperTrait
{

  /**
   * @return EntityFileCropperTrait|EntityInterface|TranslateChildInterface
   * @throws \Exception
   */
  private function getTranslateCurrentCropper(): EntityInterface
  {
    return $this->getTranslateCurrent();
  }

  /**
   * @return array
   * @throws \Exception
   */
  public function getCropperData(): array
  {
    return $this->getTranslateCurrentCropper() ? $this->getTranslateCurrentCropper()->getCropperData() : array();
  }

  /**
   * @param array $cropperData
   *
   * @return EntityTranslateMasterFileCropperTrait|EntityInterface
   * @throws \Exception
   */
  public function setCropperData(array $cropperData): EntityInterface
  {
    if($this->getTranslateCurrentCropper())
    {
      $this->getTranslateCurrentCropper()->setCropperData($cropperData);
    }
    return $this;
  }

  /**
   * @param string $filename
   *
   * @return array
   * @throws \Exception
   */
  public function getCropperDataByFilename(string $filename): array
  {
    return $this->getTranslateCurrentCropper() ? $this->getTranslateCurrentCropper()->getCropperDataByFilename($filename) : array();
  }

  /**
   * @return array
   * @throws \Exception
   */
  public function getGenerateCropperByKey(): array
  {
    return $this->getTranslateCurrentCropper() ? $this->getTranslateCurrentCropper()->getGenerateCropperByKey() : array();
  }

  /**
   * @param array $generateCropperByKey
   *
   * @return $this
   * @throws \Exception
   */
  public function setGenerateCropperByKey(array $generateCropperByKey)
  {
    if($this->getTranslateCurrentCropper())
    {
      $this->getTranslateCurrentCropper()->setGenerateCropperByKey($generateCropperByKey);
    }
    return $this;
  }

}