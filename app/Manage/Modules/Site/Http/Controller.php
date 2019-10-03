<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Manage\Modules\Site\Http;

// ------------------------------------------------------------------------
use App\Manage\Http\AccessControl\Controllers\AuthenticatedController;

/**
 * Class Controller
 * @package App\Manage\Modules\Sites\Http
 */
class Controller extends AuthenticatedController
{
    public function route($method, array $arguments = [])
    {
        if(in_array($method, ['add', 'edit'])) {
            call_user_func_array([$this, 'form'], $arguments);
        } elseif(method_exists($this, $method)) {
            call_user_func_array([$this, $method], $arguments);
        }
    }
}