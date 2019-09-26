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

namespace App\Manage\Http\AccessControl\Middleware;

// ------------------------------------------------------------------------

use App\Api\Modules\Companies\Models\Companies;
use App\Api\Modules\System\Models\Modules\Users\Notifications;
use O2System\Psr\Http\Message\ServerRequestInterface;
use O2System\Psr\Http\Server\RequestHandlerInterface;
use O2System\Security\Authentication\User\Account;

/**
 * Class UserAuthentication
 *
 * @package App\Http\AccessControl\Middleware
 */
class UserAuthentication implements RequestHandlerInterface
{
    /**
     * UserAuthentication::$user
     *
     * @var Account
     */
    protected $account;

    // ------------------------------------------------------------------------

    /**
     * UserAuthentication::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request)
    {
        if ( ! services('user')->loggedIn()) {
            redirect_url('/');
        } else if(  $user = models('users')->find(session()->account->user->id)){
            $this->account = new Account($user->getArrayCopy());
            $this->account->store('user', $user);
            if ($profile = $user->profile) {
                $this->account->store('profile', $profile);
            }
            session()->set('account', $this->account->getArrayCopy());
            globals()->store('account', $this->account);
            presenter()->store('account', $this->account);
            models(Notifications::class)->qb->orderBy('id', 'DESC');
            $notif = models(Notifications::class)->findIn([0, $user->id],'sys_module_user_recipient_id');
            presenter()->store('notifications', view(PATH_RESOURCES.'views/notifications/manage.phtml', [
                'notifications' => $notif
            ], true));

        }
    }
}