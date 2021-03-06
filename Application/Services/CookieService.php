<?php

/**
 * Cookie Service Is designated for cookies operations
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application\Services
 * @version 1.0.1
*/

namespace Application\Services;

/**
 * Cookie Service Class Is designated for cookies operations
 * 
*/
class CookieService {

	/**
	 * Sets a cookie.
	 *
	 * @param string $cookie The cookie name.
	 * @param string $value The value of the cookie.
	 * @param int $expire When will expire the cookie. Default is 30 days.
	 * @param string $path The path where you want to apply the cookie. Default is root.
	 * @param string $domain The (sub)domain where you want to apply the cookie. Default is main domain.
	 * @param bool $secure Whether the cookie will be set in a secure manner or not. Default is unsecure.
	 * @param bool $httponly Whether the cookie will be HTTP only or not. Default is not set to http only.
	 * @todo Throw exception instead using die.
	 * @return null|bool Dies with an error if the cookie name or value is empty and returns true/false if the cookie has been set.
	*/
	private static function set($cookie = null, $value = null, $expire = 2592000, $path = '/', $domain = '', $secure = false, $httponly = false) {

		if (empty($cookie)) {
			die('Cookie setter name must be defined and not empty.');
		}

		if (is_null($value)) {
			die('Cookie <strong>' . $cookie . '</strong> must have a value.');
		}

		setcookie($cookie, $value, time() + (int)$expire, $path, $domain, $secure, $httponly);
	}

	/**
	 * Gets a cookie value.
	 *
	 * @param string $cookie The cookie name for which you want its value.
	 * @todo Throw exception instead using die.
	 * @return null|bool Returns the error if the operation cannot be performed or the cookie value otherwise.
	*/
	private static function get($cookie) {

		if (empty($cookie)) {
			die('Cookie getter name must be defined and not empty.');
		}

		return (isset($_COOKIE[$cookie])) ? $_COOKIE[$cookie] : null;
	}

	/**
	 * Remove a cookie from the client.
	 *
	 * @param string $cookie The cookie that you want to remove.
	 * @param string $path The path from where you want to remove the cookie. Default is root.
	 * @param string $domain The (sub)domain from where you want to remove the cookie. Default is main domain.
	 * @return null|bool Returns the error if the operation cannot be performed and true/false if the cookie has been removed.
	*/
	public static function remove($cookie = null, $path = '/', $domain = '') {

		if (empty($cookie)) {
			die('Cookie setter name must be defined and not empty.');
		}

		setcookie($cookie, '', time() - 1000, $path, $domain);
	}
}