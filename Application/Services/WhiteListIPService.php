<?php

/**
 * White List IP Service Manipulates Access for IPs
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application\Services
 * @version 1.0.1
*/

namespace Application\Services;

use Application\Models\WhiteListIPModel;

/**
 * White List IP Service Class Manipulates Access for IPs
 * 
*/
class WhiteListIPService {

	/**
	 * Verify if the IP has access or not.
	 *
	 * @todo This method is redundant.
	 * @return null
	*/
	public static function checkIpIsWhiteListed() {
		self::_checkIpAccess(self::_getWhiteListIps());
	}

	/**
	 * Builds the list with whitelisted ips.
	 *
	 * @return array The list with whitelisted ips.
	*/
	private static function _getWhiteListIps() {

		$whiteListIps = new WhiteListIPModel;
		$whiteListIps->db->columns('ip');
		$whiteListedIps = [];

		foreach ($whiteListIps->db->getRows() as $ipData) {
			$whiteListedIps[] = $ipData->ip;
		}

		return $whiteListedIps;
	}

	/**
	 * Verifies if the IP has access or not. It allows using multiple IPs based on a mask.
	 *
	 * @param string $whiteListIps The whitelist ips.
	 * @todo The $whiteListIps should not be receive as parameter.
	 * @todo The method should cache the list of whitelist ips for next usage whitout processing it again.
	 * @todo Do not use $_SERVER['REMOTE_ADDR'].
	 * @return null If the user has access the execution will continue. Otherwise a message is sent to the client and the operation is halted
	*/
	private static function _checkIpAccess($whiteListIps) {

		if (!isset($_SERVER['REMOTE_ADDR'])) {
			return true;
		}

		$ipAddr = explode('.', $_SERVER['REMOTE_ADDR']);

		foreach ($whiteListIps as $key => $ip) {

			$ipArr = explode('.', $ip);

			foreach ($ipArr as $index => $group) {
				if ($group == '*') {
					$ipArr[$index] = $ipAddr[$index];
				}
			}

			$whiteListIps[$key] = join('.', $ipArr);
		}

		if (!in_array($_SERVER['REMOTE_ADDR'], $whiteListIps)) {
			echo $_SERVER['REMOTE_ADDR'] . ' - ';
			http_response_code(401);
			echo '401 Unauthorized';
			die;
		}
	}
}