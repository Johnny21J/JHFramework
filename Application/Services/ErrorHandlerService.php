<?php

/**
 * Error Handler Service Handle Errors and Exceptions in the Application
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application\Services
 * @version 1.0.1
*/

namespace Application\Services;

use Core\Application;

use Core\View;
use Core\Config;

/**
 * Error Handler Service Class Handle Errors and Exceptions in the Application
 * 
*/
class ErrorHandlerService {

	/**
	 * Used for all kind of errors.
	 *
	 * @param int $errorNumber Error code number.
	 * @param string $errorMessage Error message that you want to generate.
	 * @param string $errorFile The Filename where the error has been encoutered.
	 * @param int $errorLine The Line where the error has been encountered.
	 * @return null Halts the operation with a 500 error message.
	*/
	public final function All($errorNumber, $errorMessage, $errorFile, $errorLine) {

		if ($errorNumber !== NULL) {

			switch ($errorNumber) {
				case E_ERROR:
					$errorType = 'E_ERROR';
				break;
				case E_WARNING:
					$errorType = 'E_WARNING';
				break;
				case E_PARSE:
					$errorType = 'E_PARSE';
				break;
				case E_NOTICE:
					$errorType = 'E_NOTICE';
				break;
				case E_USER_ERROR:
					$errorType = 'E_USER_ERROR';

					if (strpos($errorMessage, 'MySql Error') !== false) {
						list($errorMessage, $errorType, $errorFile, $errorLine, $errorFunction, $errorLastQuery) = explode(PHP_EOL, $errorMessage);
					}
				break;
				default:
					$errorType = $errorNumber;
				break;
			}

			header(((isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : ' - ') . ' 500 Internal Server Error', true, 500);

			$errorResult = [
				'Date' 		=> date('Y:m:d H:i:s'),
				'Message' 	=> $errorMessage,
				'Type' 		=> $errorType,
				'File' 		=> $errorFile,
				'Line' 		=> $errorLine,
				'Backtrace' => debug_backtrace(),
			];

			if (isset($errorFunction)) {
				$errorResult['Function'] = $errorFunction;
			}

			if (isset($errorLastQuery)) {
				$errorResult['LastQuery'] = $errorLastQuery;
			}

			$this->_output($errorResult);
		}

		die;
	}

	/**
	 * Used for exceptions.
	 *
	 * @param object $Exception The full exception result.
	 * @return null
	*/
	public final function Exception($Exception) {

		http_response_code(500);
		echo '500 Internal Server Error';

		$errorResult = [
			'Date' 		=> date('Y:m:d H:i:s'),
			'Message' 	=> $Exception->getMessage(),
			'Type' 		=> 'Exception',
			'File' 		=> $Exception->getFile(),
			'Line' 		=> $Exception->getLine(),
			'Backtrace' => $Exception->getTrace(),
		];

		$this->_output($errorResult);
	}

	/**
	 * Display on the console or in file the encountered error or exception.
	 *
	 * @param string $errorResult The error found.
	 * @return null Outputs directly to console or file the error result.
	*/
	private function _output($errorResult) {

		$this->view = new View;

		switch (Application::environment()) {
			case 'development':
			case 'staging':

				if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') { // AJAX CALL
					
					header('Content-Type: application/json; charset=utf-8');

					echo json_encode($errorResult, JSON_FORCE_OBJECT);
				} else {

					echo $this->view->load('Error', $errorResult);
				}
			break;
		}

		$errorFileResult = [];

		foreach ($errorResult as $key => $value) {
			if ($key != 'Backtrace') {
				$errorFileResult[] = $key . ': ' . print_r($value, true);
			}
		}

		file_put_contents(dirname(__FILE__) . '/../' . Config::get('debug.log.path'), implode(PHP_EOL, $errorFileResult) . PHP_EOL . PHP_EOL, FILE_APPEND);
	}
}

$ErrorHandlerService = new ErrorHandlerService;

set_exception_handler(array($ErrorHandlerService, 'Exception'));
set_error_handler(array($ErrorHandlerService, 'All'));