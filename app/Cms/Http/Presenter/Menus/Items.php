<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */

// ------------------------------------------------------------------------

namespace Cms\Http\Presenter\Menus;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\AbstractItemStoragePattern;

/**
 * Class Items
 * @package Cms\Http\Presenter\Menus
 */
class Items extends AbstractItemStoragePattern
{
    public function render()
    {
        return implode(PHP_EOL, $this->getArrayCopy() );
    }

    public function __toString()
    {
        return $this->render();
    }
}