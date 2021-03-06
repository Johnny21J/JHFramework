<?php

/**
 * Error Controller
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application/Controllers
 * @version 1.0.1
*/

namespace Application\Controllers;

/**
 * Error Controller Class
 * 
*/
class ErrorController extends BaseController
{
    /**
     * Datagrid Listing page
     *
     * @todo Remove Blade View from this method
     * 
     * @return null 
    */
    public function Index()
    {
        $this->layout->body('Error');
        if (!empty($this->get_param(2))) {
            $this->layout->assign('message', base64_decode($this->get_param(2)));
            $this->layout->display('show_message', 'show');
        } else {
            $this->layout->display('show_message', 'hide');
        }
        $this->render(null, 'string');
    }
}