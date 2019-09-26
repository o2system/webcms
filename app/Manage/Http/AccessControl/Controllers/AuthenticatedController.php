<?php
/**
 * This file is part of the NEO ERP Application.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Manage\Http\AccessControl\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Http\AccessControl\Middleware\UserAuthentication;
use App\Manage\Http\AccessControl\Middleware\UserAuthorization;
use App\Manage\Http\Controller;

/**
 * Class AuthenticatedController
 *
 * @package App\Http\AccessControl\Controllers
 */
class AuthenticatedController extends Controller
{
    /**
     * AuthenticatedController::__construct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();
        // Register user authentication middleware
        middleware()->register( new UserAuthentication() );
    }
}