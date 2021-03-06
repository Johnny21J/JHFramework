<?php

/**
 * Breadcrumb Service Builds and generates a Breadcrumb
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
 * Breadcrumb Service Class Builds and generates a Breadcrumb
 * 
*/
class BreadcrumbService
{
    /**
     * @var array $breadcrumb Contains the resulting breadcrumb.
    */
    private static $breadcrumb = null;

    /**
     * Add item to breadcrumb.
     *
     * @param array $data Should: title, link and icon of the item.
     * @throws \Exception Icon must be supplied.
     * @throws \Exception Title must be supplied.
     * @return null
    */
    public static function add($data)
    {
        if (!array_key_exists('icon', $data)) {
            throw new \Exception("Icon must be supplied", 1);
        }

        if (!array_key_exists('title', $data)) {
            throw new \Exception("Title must be supplied", 1);
        }

        self::$breadcrumb = (is_null(self::$breadcrumb)) ? [$data] : array_merge(self::$breadcrumb, [$data]);
    }

    /**
     * Gets the resulting breadcrumb.
     *
     * @return string The resulting breadcrumb.
    */
    public static function get()
    {
        return implode(array_map(function($bc) {

            $item = ((isset($bc['link'])) ? 
                    '<a href="' . $bc['link'] . '"><span class="' . $bc['icon'] . '"></span> ' . $bc['title'] . '</a>' : 
                    '<span class="bTitle"><span class="' . $bc['icon'] . '"></span> '.$bc['title']) . '</span>';

            return '<li>' . $item . '</li>';

        }, self::$breadcrumb));
    }
}