<?php

/**
 * Article Category Model.
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
 * Article Category Model Class.
 * 
*/
class ArticleCategoryModel extends DBModel {

	/**
	 * @var string $table The working table name.
	*/
	protected $table = 'articles_categories';

	/**
	 * Get all records.
	 * 
	 * @return array The data result of the query.
	*/
	public function getAll() {

		$this->db->columns('id, name, slug');

		$this->db->order('name');

		return $this->db->getRows();
	}
}