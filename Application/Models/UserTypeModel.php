<?php

/**
 * User Type Model.
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application\Models
 * @version 1.0.1
*/

namespace Application\Models;

use Core\DBModel;

/**
 * User Type Model Class.
 * 
*/
class UserTypeModel extends DBModel {

	/**
	 * @var string $table The working table name.
	*/
	protected $table = 'users_types';

	/**
	 * Get all records.
	 * 
	 * @return array The data result of the query.
	*/
	public function getAll() {
		$this->db->columns('id, account_type AS name');
		$this->db->order('account_type');
		return $this->db->getRows();
	}
}