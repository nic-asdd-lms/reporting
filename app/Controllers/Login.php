<?php
namespace App\Controllers;



use App\Models\UserMasterModel;
use App\Config\App;

//session_start(); //we need to start session in order to access it through CI

class Login extends BaseController
{

	protected $helpers = ['form'];


	public function index()
	{
		$validation = \Config\Services::validation();
		if (!$this->request->is('post')) {
			return view('header_view') . view('login_view') . view('footer_view');
		}

		$rules = [
			'username' => 'required',
			'password' => 'required'
		];

		if (!$this->validate($rules)) {
			return view('header_view') . view('login_view') . view('footer_view');
		}

	}

	public function user_login_process()
	{
		$request = service('request');
		if (!$this->request->is('post')) {

			return view('header_view') . view('login_view') . view('footer_view');
		}

		$rules = [
			'username' => 'required',
			'password' => 'required'
		];

		if (!$this->validate($rules)) {

			return view('header_view') . view('login_view') . view('footer_view');
		} else {


			$data = array(
				'username' => $request->getPost('username'),
				'password' => $request->getPost('password')
			);
			$user = new UserMasterModel();
			$result = $user->login($data);
			if ($result == TRUE) {

				$username = $request->getPost('username');
				$result = $user->read_user_information($username);

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

						return $this->response->redirect(site_url('/'));
						//  $red = $config->item('base_url_other');
						//  redirect($red, 'refresh');
					} elseif ($session->get('role') == 'MDO_ADMIN') {
						$data['role'] = 'MDO_ADMIN';
						$data['logged_in'] = true;
						$data['ministry'] = $session->get('ministry');
						$data['department'] = $session->get('department');
						$data['organisation'] = $session->get('organisation');

						return $this->response->redirect(site_url('/'));
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					} elseif ($session->get('role') == 'CBC_ADMIN') {
						$data['role'] = 'CBC_ADMIN';
						$data['logged_in'] = true;

						return $this->response->redirect(site_url('/'));
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					} elseif ($session->get('role') == 'CBP_ADMIN') {

						$data['role'] = 'CBP_ADMIN';
						$data['logged_in'] = true;
						$data['ministry'] = $session->get('ministry');
						$data['department'] = $session->get('department');
						$data['organisation'] = $session->get('organisation');
						return $this->response->redirect(site_url('/'));
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					} elseif ($session->get('role') == 'DOPT_ADMIN') {

						$data['role'] = 'DOPT_ADMIN';
						$data['logged_in'] = true;
						return $this->response->redirect(site_url('/'));
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					} elseif ($session->get('role') == 'ATI_ADMIN') {

						$data['role'] = 'ATI_ADMIN';
						$data['logged_in'] = true;
						$data['ministry'] = $session->get('ministry');
						$data['department'] = $session->get('department');
						$data['organisation'] = $session->get('organisation');
						return $this->response->redirect(site_url('/'));
						// $red = $this->config->item('base_url_other').'/Admin/email_data';
						// redirect($red, 'refresh');
					}



				} else {
					$data = array(
						'error_message' => 'Invalid Username or Password'
					);

					return view('header_view').view('login_view', $data).view('footer_view');
				}
			}
		}
	}
	// Logout from admin page

	public function logout()
	{
		// Removing session data
		$session = \Config\Services::session();
		$sess_array = array(
			'username' => '',
			'role' => '',
			'password' => '',
			'logged_in' => false
		);
		$session->remove($sess_array);
		$_SESSION['logged_in'] = false;
		$data['message_display'] = 'Successfully Logout';

		//$red = $this->config->item('base_url_other').'/login/index';
		return view('header_view') . view('login_view', $data) . view('footer_view');
	}
}