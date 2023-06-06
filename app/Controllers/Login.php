<?php
namespace App\Controllers;

use App\Models\UserMasterModel;
use App\Models\UserEnrolmentCourse;
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
			// return view('header_view') . view('error_general').view('footer_view');
		}
	}

	public function getRoles($userId)
	{
		try {
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
		catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
			// return view('header_view') . view('error_general').view('footer_view');
		}
	}

	public function getUserCourseList(){
		try{
			$request = service('request');
			$username = $request->getPost('apiusername');
			$password = $request->getPost('apipassword');
			$email = $request->getPost('email');
			$user = new UserMasterModel();
			$coursedata = ""; 
			$success = 1 ; 
			$isValid = $user->validAPIUser($username,$password);
			if($isValid){
				$msg = "Valid API user"; 
				$userdetails = $user->getUserIDbyEmail($email);
				if($userdetails){
						$msg = "Email Exists";
						$userid = $userdetails[0]['user_id'] ;  
						// get Course Data based on $userID
						$usercourse = new UserEnrolmentCourse() ; 
						$org = '' ; 
						$orderBy = 1 ; 
						$orderDir = 'asc';
						$courses = $usercourse->getUserWiseEnrolment($userid, $org, -1, 0, '', $orderBy, $orderDir);
						$coursedata = $courses->getResultArray() ; 
						$success = 0;  
						$msg = "Course List";
				}
				else {
					$msg = "Email Doesn't Exist";
				}
				// get Course list by userID and send it in response
			}
			else {
				$msg = "InValid API user";
				}
				return response()->setContentType('application/json')                             
                 ->setStatusCode(200)
                 ->setJSON(['status'=>$success , 'message' => $msg,'data'=>$coursedata]);
			}
			catch (\Exception $e) {
				throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
				// return view('header_view') . view('error_general').view('footer_view');
			}		
	}

	public function user_login_process($email){
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
					} if ($session->get('role') == 'IGOT_TEAM_MEMBER') {
						$data['role'] = 'IGOT_TEAM_MEMBER';
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
						return $this->response->redirect(base_url('/dashboard/dopt?ati=&program='));
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
			// return view('header_view') . view('error_general').view('footer_view');
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
				
			if (isset($_SERVER['HTTP_COOKIE'])) {
				$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
				foreach($cookies as $cookie) {
					$parts = explode('=', $cookie);
					$name = trim($parts[0]);
					setcookie($name, '', time()-1000);
					
				}
				$headers[] = "x-authenticated-user-token: " . $_COOKIE['token'];
			$headers[] = 'Content-Type: application/x-www-form-urlencoded';
			$headers[] = "Authorization: " . $GLOBALS['API_KEY'];
			
				$logoutUrl = $GLOBALS['IGOT_URL'] . 'auth/realms/sunbird/protocol/openid-connect/logout' ;
			// Create a new cURL resource
			$ch = curl_init($logoutUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST,           1 );
			curl_setopt($ch, CURLOPT_POSTFIELDS,     "client_id=portal&refresh_token=".$_COOKIE['refreshToken'] ); 

			$userInfo = curl_exec($ch);
	
			$users = json_decode($userInfo);
			// Close cURL resource
			curl_close($ch);
			
				return $this->response->redirect(base_url('/'));
			}
			
			return view('header_view') . view('logged_out') . view('footer_view');
		} catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
			// return view('header_view') . view('error_general').view('footer_view');
		}
	}
	public function unauthorized()
	{ // First Check if the user is logged in or not
		//$data['userID'] = $this->input->get('email');  
		return view('header_view') . view('unauthorized_view') . view('footer_view');
	}
}