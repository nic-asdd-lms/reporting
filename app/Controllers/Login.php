<?php
namespace App\Controllers;

use App\Models\UserMasterModel;
use App\Config\App;
use App\Config\Assets;

//session_start(); 							//we need to start session in order to access it through CI

class Login extends BaseController
{

	protected $helpers = ['form'];

	public function checkIgotUser()
	{ // First Check if the user is logged in or not
		//$data['userID'] = $this->input->get('email');  
			return view('keyCloakLogin');
	}

	public function index()
	{
		try {

			// IN $_COOKIE we are getting uid which is user ID now we have to hit user read API and get Appropriate data and manage role 
			$this->getRoles($_COOKIE['uid']);

		} catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function getRoles($userId)
	{
		
		$headers[] = "x-authenticated-user-token: " . $_COOKIE['token'];
		$headers[] = 'Content-Type: application/json';
		$headers[] = "Authorization: " . $GLOBALS['API_KEY'];
		// API URL
		$profileUrl = $GLOBALS['IGOT_URL'] . 'api/user/v2/read/' . $userId;
		// Create a new cURL resource
		$ch = curl_init($profileUrl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$userInfo = curl_exec($ch);

		$users = json_decode($userInfo);
		// Close cURL resource
		curl_close($ch);
		
		$email = $users->result->response->profileDetails->personalDetails->primaryEmail;
		
		$this->user_login_process($email);
		

	}
	public function user_login_process($email)
	{
		try {

			$request = service('request');

			$user = new UserMasterModel();
			$result = $user->login($email);

			if ($result == TRUE) {

				// $username = $request->getPost('username');
				$result = $user->read_user_information($email);

				if ($result != false) {

					$session_data = [
						'role' => $result[0]->role,
						'username' => $result[0]->username,
						'ministry' => $result[0]->ministry,
						'department' => $result[0]->department,
						'organisation' => $result[0]->organisation,
						'logged_in' => true
					];
					// Add user data in session
					$session = \Config\Services::session();
					$session->set($session_data);
					$_SESSION['logged_in'] = true;
					if ($session->get('role') == 'SPV_ADMIN') {
						$data['role'] = 'SPV_ADMIN';
						$data['logged_in'] = true;

						//  $red = $config->item('base_url_other');
						//  redirect($red, 'refresh');
					} elseif ($session->get('role') == 'MDO_ADMIN') {
						$data['role'] = 'MDO_ADMIN';
						$data['logged_in'] = true;
						$data['ministry'] = $session->get('ministry');
						$data['department'] = $session->get('department');
						$data['organisation'] = $session->get('organisation');

						// return $this->response->redirect('/home');
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					} elseif ($session->get('role') == 'CBC_ADMIN') {
						$data['role'] = 'CBC_ADMIN';
						$data['logged_in'] = true;

						// return $this->response->redirect('/home');
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					} elseif ($session->get('role') == 'CBP_ADMIN') {

						$data['role'] = 'CBP_ADMIN';
						$data['logged_in'] = true;
						$data['ministry'] = $session->get('ministry');
						$data['department'] = $session->get('department');
						$data['organisation'] = $session->get('organisation');
						// return $this->response->redirect('/home');
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					} elseif ($session->get('role') == 'DOPT_ADMIN') {

						$data['role'] = 'DOPT_ADMIN';
						$data['logged_in'] = true;
						// return $this->response->redirect('/home');
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					} elseif ($session->get('role') == 'ATI_ADMIN') {

						$data['role'] = 'ATI_ADMIN';
						$data['logged_in'] = true;
						$data['ministry'] = $session->get('ministry');
						$data['department'] = $session->get('department');
						$data['organisation'] = $session->get('organisation');
						// return $this->response->redirect('/home');
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					}
					return $this->response->redirect(base_url('/home'));
				} else {
					return view('header_view') . view('unauthorized_view') . view('footer_view');
				}
			} else {
				print_r($email);
				return $this->response->redirect(base_url('/unauthorized'));
			}

		} catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
		}

	}
	// Logout from admin page

	public function logout()
	{
		// Removing session data
		try {


			$session = \Config\Services::session();
			$user = $session->get('username');
			$sess_array = array(
				'username' => '',
				'role' => '',
				'ministry' => '',
				'department' => '',
				'organisation' => '',
				'password' => '',
				'logged_in' => false
			);
			$session->remove($sess_array);
			$_SESSION['logged_in'] = false;
				
			if (isset($_COOKIE['token'])) {
				unset($_COOKIE['uid']);
				unset($_COOKIE['token']);
				setcookie('uid', null, -1, '/');
				setcookie('token', null, -1, '/');
				return $this->response->redirect(base_url('/'));
			}
			// else {
			// 	return false;
			// }
			// $data['message_display'] = 'Successfully Logout';
			// setcookie($_COOKIE['uid'], "", time() - 3600);
			// print_r($_COOKIE);
			// die;
			// return $this->response->redirect(base_url('/checkIgotUser'));
			//$red = $this->config->item('base_url_other').'/login/index';
			return view('header_view') . view('logged_out') . view('footer_view');
		} catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
		}
	}
	public function unauthorized()
	{ // First Check if the user is logged in or not
		//$data['userID'] = $this->input->get('email');  
		return view('header_view') . view('unauthorized_view') . view('footer_view');
	}
}