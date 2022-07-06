<?php
/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Austral\EntityTranslateBundle\Annotation;

use Austral\EntityBundle\Annotation\AustralEntityAnnotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
final class Translate extends AustralEntityAnnotation
{

  /**
   * @var string
   */
  public string $relationClass;

  /**
   * @var string|null
   */
  public ?string $fieldname = "translate";

  /**
   * @param string|null $relationClass
   * @param string|null $fieldname
   */
  public function __construct(string $relationClass, ?string $fieldname = "translate") {
    $this->relationClass = $relationClass;
    $this->fieldname = $fieldname;
  }

}