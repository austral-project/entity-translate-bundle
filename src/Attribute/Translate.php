<?php

/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityTranslateBundle\Attribute;

use Austral\EntityBundle\Attribute\AustralEntityAttribute;

/**
 * Class Translate
 *
 * @Attribute
 *
 * @Target("CLASS")
 *
 * @author  Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Translate extends AustralEntityAttribute
{
  /**
   * @var class-string
   */
  public string $relationClass;

  /**
   * @var string
   */
  public string $fieldname = 'translate';

  /**
   * @param class-string     $relationClass
   * @param non-empty-string $fieldname
   */
  public function __construct(string $relationClass, string $fieldname = 'translate')
  {
    $this->relationClass = $relationClass;
    $this->fieldname = $fieldname;
  }
}
