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

namespace App\Models\System;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Acl\Datastructures\Signature;
use O2System\Framework\Libraries\Acl\Datastructures\Profile;
use O2System\Framework\Libraries\Acl\Datastructures\Roles;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Libraries\Acl\Datastructures\Account;
use O2System\Framework\Models\Sql\Traits\RelationTrait;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Users
 *
 * @package O2System\Framework\Sql\Models\Users
 */
class Users extends Model
{
    use RelationTrait;

    public $table = 'sys_users';

    public function profile()
    {
        return $this->getProfile($this->row->id);
    }

    public function insert(array $data)
    {
        return $this->qb->table($this->table)->insert($data);
    }

    public function findAccount($criteria, $field = null)
    {
        if (false !== ($account = parent::find($criteria, $field, 1))) {
            return new Account(
                [
                    'id'       => $account->id,
                    'email'    => $account->email,
                    'msisdn'   => $account->msisdn,
                    'username' => $account->username,
                    'password' => $account->password,
                    'pin'      => $account->pin,
                ],
                false);
        }

        return false;
    }

    public function updateAccount(Account $account)
    {
        return $this->qb->table($this->table)
            ->where('username', $account->username)
            ->update($account->getArrayCopy());
    }

    public function getProfile($id_user, $scope = 'ALL')
    {
        $result = $this->qb
            ->table('sys_users_profiles')
            ->join('sys_users', 'sys_users.id = sys_users_profiles.id_sys_user')
            ->getWhere(['id_sys_user' => $id_user], 1);

        if ($result->count()) {
            return new Profile($result->first());
        }

        return false;
    }

    public function updateProfile(Profile $profile)
    {

    }

    public function rawUpdateProfile($data)
    {
        $profile = session()->get('account')->profile;

        $this->db->table('sys_users_profiles')->where([
            'id' => $profile->id,
        ])->update($data);
    }

    public function getRoles($id_sys_user)
    {
        $result = $this->qb
            ->table('sys_users_roles')
            ->select([
                'sys_modules_users_roles.id',
                'sys_modules_users_roles.code',
                'sys_modules_users_roles.label',
                'sys_modules_users_roles.description',
            ])
            ->join('sys_modules_users_roles', 'sys_modules_users_roles.id = sys_users_roles.id_sys_module_user_role',
                'INNER')
            ->where(['id_sys_user' => $id_sys_user])
            ->get();

        if ($result->count()) {

            $roles = new Roles();

            foreach ($result as $row) {
                $roles->register(new Roles\Role($row), $row->code);
            }

            return $roles;
        }

        return false;
    }

    public function getRolesAccess($id_sys_user)
    {
        $namespaces = [];
        $roles = new ArrayIterator();

        foreach (modules() as $module) {
            if ( ! in_array($module->getType(), ['FRAMEWORK', 'KERNEL'])) {
                $namespaces[] = rtrim($module->getNamespace(), '\\') . '\\\\';
            }
        }

        if (count($namespaces)) {
            $result = $this->qb
                ->table('sys_users_roles')
                ->select([
                    'sys_modules_users_roles_access.id',
                    'sys_modules_users_roles_access.label',
                    'sys_modules_users_roles_access.segments',
                    'sys_modules_users_roles_access.permission',
                    'sys_modules_users_roles_access.privileges',
                ])
                ->join('sys_modules_users_roles',
                    'sys_modules_users_roles.id = sys_users_roles.id_sys_module_user_role', 'INNER')
                ->join('sys_modules_users_roles_access',
                    'sys_modules_users_roles_access.id_sys_module_user_role = sys_modules_users_roles.id', 'INNER')
                ->join('sys_modules', 'sys_modules.id = sys_modules_users_roles.id_sys_module', 'INNER')
                ->where(['sys_users_roles.id_sys_user' => $id_sys_user])
                ->whereIn('sys_modules.namespace', $namespaces)
                ->get();

            if ($result->count()) {
                foreach ($result as $row) {
                    $roles[ $row->segments ] = new Roles\Access($row);
                }
            }

            $result = $this->qb
                ->table('sys_users_roles')
                ->select([
                    'sys_users_roles_access.id',
                    'sys_users_roles_access.label',
                    'sys_users_roles_access.segments',
                    'sys_users_roles_access.permission',
                    'sys_users_roles_access.privileges',
                ])
                ->join('sys_modules_users_roles',
                    'sys_modules_users_roles.id = sys_users_roles.id_sys_module_user_role', 'INNER')
                ->join('sys_modules', 'sys_modules.id = sys_modules_users_roles.id_sys_module', 'INNER')
                ->join('sys_users_roles_access', 'sys_users_roles_access.id_sys_user_role = sys_users_roles.id',
                    'INNER')
                ->where(['sys_users_roles.id_sys_user' => $id_sys_user])
                ->whereIn('sys_modules.namespace', $namespaces)
                ->get();

            if ($result->count()) {
                foreach ($result as $row) {
                    $roles[ $row->segments ] = new Roles\Access($row);
                }
            }
        }

        if ($roles->count()) {
            return $roles;
        }

        return false;
    }

    public function findSignature($ssid)
    {
        $result = $this->qb
            ->table('sys_users_signatures')
            ->where('code', $ssid)
            ->get(1);

        if ($result->count()) {
            return $this->findAccount($result->first()->id_sys_user, 'id');
        }

        return false;
    }

    public function insertSignature(Signature $signature)
    {
        return $this->qb->table('sys_users_signatures')->insert([
            'id_sys_user' => $signature->id_sys_user,
            'code'        => $signature->code,
            'ip_address'  => $signature->ip_address,
            'user_agent'  => $signature->user_agent,
        ]);
    }

    public function deleteSignature(Account $account)
    {
        return $this->qb->table('sys_users_signatures')->delete(['id_sys_user' => $account->id]);
    }
}