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

namespace App\Manage\Http;

// ------------------------------------------------------------------------

//use App\Manage\Http\Presenter\Page;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Controller
 *
 * @package App\Http
 */
class Controller extends \App\Http\Controller
{
    /**
     * Controller::__construct
     *
     * @var \O2System\Framework\Models\Sql\Model|string
     */
    public $model;

    // ------------------------------------------------------------------------

    /**
     * Controller::__reconstruct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->meta->title->replace('Nitro by O2System');

        $className = get_class_name($this);

        // Set Page Title and Header
        presenter()->page
            ->setHeader($className)
            ->setTitle($className);

        if (empty($this->model)) {
            $controllerClassName = get_called_class();
            $modelClassName = str_replace(['App', 'Controllers'], ['App\Api', 'Models'], $controllerClassName);

            if (class_exists($modelClassName)) {
                $this->model = new $modelClassName();
            }
        } elseif (class_exists($this->model)) {
            $this->model = new $this->model();
        }

        // Set Breadcrumb
        $segments = server_request()->getUri()->segments->getArrayCopy();
        $numSegments = count($segments);
        $breadcrumbSegments = [];
        for ($i = 0; $i < $numSegments; $i++) {
            $breadcrumbSegments[ $i ] = $segments[ $i ];
            if ($i == $numSegments) {
                presenter()->page->breadcrumb->createList(new Link(
                    language(strtoupper(underscore($segments[ $i ])))
                ));
                break;
            } else {
                presenter()->page->breadcrumb->createList(new Link(
                    language(strtoupper(underscore($segments[ $i ]))),
                    base_url($breadcrumbSegments)
                ));
            }
        }

        presenter()->page->breadcrumb->attributes->addAttributeClass('p-0 border-0 mb-0 ml-3');
    }
}
