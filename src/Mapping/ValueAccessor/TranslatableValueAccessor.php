<?php

/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityTranslateBundle\Mapping\ValueAccessor;

use Austral\Contracts\Entity\EntityInterface;
use Austral\Contracts\Translate\TranslateMasterInterface;
use Austral\EntityBundle\Mapping\ValueAccessor\ValueAccessorInterface;
use Austral\EntityBundle\PropertyAccess\FieldAccessor;

/**
 * Reads and writes on the entity first, then, for a master, on its current translation
 * through the master's own __get/__set (see EntityTranslateMasterTrait).
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
final class TranslatableValueAccessor implements ValueAccessorInterface
{
  /**
   * {@inheritDoc}
   */
  public function getValue(EntityInterface $object, string $fieldname): mixed
  {
    if (FieldAccessor::isReadable($object, $fieldname))
    {
      return FieldAccessor::getValue($object, $fieldname);
    }

    return $object instanceof TranslateMasterInterface ? $object->__get($fieldname) : null;
  }

  /**
   * {@inheritDoc}
   *
   * @throws \LogicException when a master has the field neither on itself nor on its translation
   */
  public function setValue(EntityInterface $object, string $fieldname, mixed $value = null): void
  {
    if (FieldAccessor::isWritable($object, $fieldname))
    {
      FieldAccessor::setValue($object, $fieldname, $value);

      return;
    }
    if ($object instanceof TranslateMasterInterface)
    {
      $object->__set($fieldname, $value);
    }
  }
}
