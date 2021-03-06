<?php

/**
 * Login Controller
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application/Controllers
 * @version 1.0.1
*/

namespace Application\Controllers;

use Application\Models\UserModel;

use Application\Services\AuthService;

use Application\Actions\MailAction;

use Core\Config;

/**
 * Login Controller Class
 * 
*/
class LoginController extends BaseController {

	/**
	 * @var string $layout Application View Layout
	*/
	protected $layout = 'LayoutBasic';

	/**
	 * @var int $loginAttempts Number of Loggin Attempts
	*/
	private $loginAttempts 			= 5;

	/**
	 * @var int $loginAllowAccessAfter After how many seconds the user can login again if he gets blocked.
	*/
	private $loginAllowAccessAfter 	= 900;

	/**
	 * Datagrid Listing page
	 * 
	 * @return null 
	*/
	public function Index() {

		if (!is_null($this->session->login)) {

			$logged = $this->session->login;

			if ($logged) {
				$this->redirect(Config::get('path.web') . 'Dashboard');
			}
		}

		if (isset($this->cookie->logged) && !is_null($this->cookie->logged) && !is_null($this->cookie->user_id) && !is_null($this->cookie->user_name) && !is_null($this->cookie->user_email) && !is_null($this->cookie->user_type) && $this->cookie->logged) {

			$this->session->user = [
				'id' 			=> $this->cookie->user_id,
				'name' 			=> $this->cookie->user_name,
				'email' 		=> $this->cookie->user_email,
				'user_type' 	=> $this->cookie->user_type,
			];

			$this->session->login = true;

			$this->redirect(Config::get('path.web') . 'Dashboard');
		}

		$token = (Config::get('modules')['Security']) ? \Application\Services\SecurityService::GenerateCode() : uniqid();

		$this->session->flash->token = $token;

		$this->render('Login', [
			'token'	=> $token,
		]);
	}

	/**
	 * User Login
	 * 
	 * @return null 
	*/
	public function Login() {

		if (isset($this->post['login_password'])) {

			if ($this->session->flash->token != $this->post['token']) {

				$this->session->flash->message = 'Bad request';
				$this->session->flash->success = false;

				$this->redirect(Config::get('path.web') . 'Index');
			}

			if (AuthService::checkLogin($this->post['login_email'], $this->post['login_password'], $this->loginAttempts, $this->loginAllowAccessAfter, $this->post('keep_me_logged', ''))) {

				$this->redirect(Config::get('path.web') . 'Dashboard');
				
			} else {

				if ($this->session->locked) {

					$this->session->flash->message = 'Account locked. Too many login attempts. Try again later or contact system administrator.';
					$this->session->flash->success = false;

					$this->redirect(Config::get('path.web') . 'Index');
				}

				$this->session->flash->message = 'Login incorrect';
				$this->session->flash->success = false;

				$this->redirect(Config::get('path.web') . 'Index');
			}
		} else {

			$this->session->flash->message = 'Bad request';
			$this->session->flash->success = false;

			$this->redirect(Config::get('path.web') . 'Index');
		}
	}

	/**
	 * Password Lost Page
	 * 
	 * @return null 
	*/
	public function PasswordLost() {
		$this->render('PasswordLost', []);
	}

	/**
	 * Sends an email with instructions for reseting password.
	 * 
	 * @return null 
	*/
	public function PasswordLostExec() {

		$UserModel = new UserModel();
		$adminUser = $UserModel->getUserByEmail($this->post['password_lost_email']);

		if ($adminUser) {

			$code = (Config::get('modules')['Security']) ? \Application\Services\SecurityService::GenerateCode() : uniqid();

			$UserModel->update([
				'code' => $code,
			], $adminUser->id);

			MailAction::sendPasswordResetEmail($this->post['password_lost_email'], $code);

		} else {
			// do not tell user that the accound does not exist - security reason
		}

		$this->session->flash->message = 'An email has been sent to specified address with reset password instructions. Please be sure that this email is associated with an account.';
		$this->session->flash->success = true;
		
		$this->redirect(Config::get('path.web') . 'Index/PasswordLost');
	}

	/**
	 * Password Reset Page.
	 * 
	 * @return null 
	*/
	public function PasswordReset() {

		$UserModel = new UserModel();
		$adminUser = $UserModel->getUserByCode($this->get['code']);

		if (!$adminUser) {

			$this->session->flash->message = 'Your reset link is wrong or expired.';
			$this->session->flash->success = false;

			$this->redirect(Config::get('path.web') . 'Index');
		}


		$this->render('PasswordReset', [
			'code' => $this->get['code']
		]);
	}

	/**
	 * Resets the User Password.
	 * 
	 * @return null 
	*/
	public function PasswordResetExec() {

		$UserModel = new UserModel();
		$adminUser = $UserModel->getUserByCode($this->get['code']);

		if ($adminUser) {

			$UserModel->update([
				'password' 	=> sha1($this->post['password_reset']),
				'code' 		=> '',
			], $adminUser->id);

			$this->session->flash->message = 'Your password has been reset successfully.';
			$this->session->flash->success = true;

		} else {

			$this->session->flash->message = 'Your reset link is wrong or expired.';
			$this->session->flash->success = false;
		}

		$this->redirect(Config::get('path.web') . 'Index');
	}

	/**
	 * Logout user from application
	 *
	 * @return null
	*/
	public function Logout() {

		$this->session->remove('login');
		$this->session->remove('user');
		$this->session->destroy();

		$this->cookie->remove('logged');
		$this->cookie->remove('user_id');
		$this->cookie->remove('user_name');
		$this->cookie->remove('user_email');
		$this->cookie->remove('user_type');

		$this->redirect(Config::get('path.web'));
	}
}