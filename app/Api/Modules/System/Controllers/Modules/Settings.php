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

namespace App\Api\Modules\System\Controllers\Modules;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Http\Controller;
use App\Api\Modules\System\Models;

/**
 * Class Settings
 * @package App\Api\Modules\System\Controllers\Modules
 */
class Settings extends Controller
{
    /**
     * Settings::index
     */
    public function index()
    {
        if ($get = input()->get()) {
            if (false !== ($result = Models\Modules\Settings::withPaging()->findWhere([
                    'id_sys_module' => $get->id_sys_module,
                ]))) {
                $this->sendPayload($result);
            } else {
                $this->sendError(204);
            }
        } else {
            $this->sendError(403);
        }
    }
    /**
     * Settings::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_sys_module',
            'label'    => 'ID Sys Module',
            'rules'    => 'required|integer',
            'messages' => 'ID Sys Module cannot be empty and must be integer',
        ],
        [
            'field'    => 'key',
            'label'    => 'Key',
            'rules'    => 'required',
            'messages' => 'Key cannot be empty!',
        ],
        [
            'field'    => 'value',
            'label'    => 'Value',
            'rules'    => 'optional',
        ],
    ];
}