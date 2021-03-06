<?php

/**
 * Pager Service Datagrid Pager builder
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application\Services
 * @version 1.0.1
*/

namespace Application\Services;

use Core\Config;

/**
 * Pager Service Class Datagrid Pager builder
 * 
*/
class PagerService {

	/**
	 * Gets the pager.
	 *
	 * @param int $totalRecords The number of total records you want to display on a page.
	 * @return string The HTML pager.
	*/
	public static function getPager($totalRecords) {

		$session 				= new SessionService;
		$currentRecordsPerPage 	= (isset($session->recordsPerPage[CONTROLLER_SHORT_NAME])) ? $session->recordsPerPage[CONTROLLER_SHORT_NAME] : Config::get('pager.recordsPerPageDefault');

		$pager 							= new \stdClass();
		$pager->pages 					= ceil($totalRecords / $currentRecordsPerPage);

		$pager->itemsPerPageSelector 	= self::_getItemsPerPageSelector($currentRecordsPerPage);
		$pager->paginator 				= self::_buildPager($pager->pages);

		return $pager;
	}

	/**
	 * Gets the SQL position to position limits.
	 *
	 * @return string The SQL limits positions.
	*/
	public static function getLimits() {

		$session 				= new SessionService;

		$currentRecordsPerPage 	= (isset($session->recordsPerPage[CONTROLLER_SHORT_NAME])) ? $session->recordsPerPage[CONTROLLER_SHORT_NAME] : Config::get('pager.recordsPerPageDefault');

		$from = CURRENT_PAGE * $currentRecordsPerPage - $currentRecordsPerPage;

		return $from . ', ' . $currentRecordsPerPage;
	}

	/**
	 * Gets Items per page Selector.
	 *
	 * @param int $currentRecordsPerPage The current number of the records displayed in the Datagrid.
	 * @return string The HTML containing selector of records per page.
	*/
	private static function _getItemsPerPageSelector($currentRecordsPerPage) {

		$itemsPerPageSelectorOptions = [];

		foreach (Config::get('pager.recordsPerPage') as $recordsPerPage) {
			$itemsPerPageSelectorOptions[] = '<option ' . (($currentRecordsPerPage == $recordsPerPage) ? 'selected="selected"' : '') . ' value="' . $recordsPerPage . '">' . $recordsPerPage . '</option>';
		}

		$itemsPerPageSelector = '
			<div class="nav-item" style="float: right;">
		        <span class="itemsPerPageLabel">Items per page:</span>
		        <select onchange="document.location = config.webpath + \'Index/UpdateItemsPerPage/' . CONTROLLER_SHORT_NAME . '/\' + this.value; " name="update-items-per-page" class="form-control w110 fl">' . implode(PHP_EOL, $itemsPerPageSelectorOptions) . '</select>
			</div>
		';

		return $itemsPerPageSelector;
	}

	/**
	 * Build pager.
	 *
	 * @param int $pages Number of pages available of the datagrid.
	 * @return string The HTML with the pager.
	*/
	private static function _buildPager($pages) {

		$paginator = '<ul class="pagination">';

		for ($i=1; $i <= $pages; $i++) {

			if (($i > CURRENT_PAGE - 10 && $i < CURRENT_PAGE + 10) || ($i == $pages) || ($i == 1)) {

				$paginator .= '
					<li class="' . ((CURRENT_PAGE == $i) ? 'active' : '') . '">
						<a href="' . Config::get('path.web') . CONTROLLER_SHORT_NAME . '/Index/' . $i . '">' . $i . '</a>
					</li>';
			}

			if ((CURRENT_PAGE > 11 && $i == 2) || (CURRENT_PAGE < $pages - 10 && $i == $pages - 1)) {
				$paginator .= '<li><a href="">...</a></li>';
			}
		}

		$paginator .= '</ul>';

		return $paginator;
	}
}
