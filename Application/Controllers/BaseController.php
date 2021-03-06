<?php

/**
 * Base Controller - contains global logic for controllers
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application/Controllers
 * @version 1.0.1
*/

namespace Application\Controllers;

use Core\Controller;
use Core\Config;

use Application\Services\BreadcrumbService;

/**
 * Base Controller Class - contains global logic for controllers
 * 
*/
class BaseController extends Controller {

	/**
	 * General Setups
	 * 
	 * @return null 
	*/
	public function __construct() {

		parent::__construct();

		BreadcrumbService::add([
			'title' => 'Dashboard',
			'link' 	=> Config::get('path.web') . 'Dasboard',
			'icon' 	=> 'fa fa-home',
		]);
	}
}