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

namespace Site\Controllers;

// ------------------------------------------------------------------------

use Site\Http\Controller;

/**
 * Class Frontpage
 * @package Site\Controllers
 */
class Frontpage extends Controller
{
    public function index()
    {
        view('frontpage');
    }
}