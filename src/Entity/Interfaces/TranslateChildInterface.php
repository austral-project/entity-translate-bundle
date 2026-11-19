<?php

/*
 * This file is part of the Austral EntityTranslate Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityTranslateBundle\Entity\Interfaces;

/**
 * Kept for backward compatibility with 3.x projects.
 *
 * @deprecated since 4.0, use \Austral\Contracts\Translate\TranslateChildInterface instead.
 *
 * @template T of object
 *
 * @extends \Austral\Contracts\Translate\TranslateChildInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface TranslateChildInterface extends \Austral\Contracts\Translate\TranslateChildInterface
{
}
