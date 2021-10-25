<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Nwdthemes Standalone Slider Revolution
 *
 * @package     StandaloneRevslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2015. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

class Account extends RS_Controller {

	/**
	 *	Constructor
	 */
	public function __construct() {
		parent::__construct();
        force_config_ssl();
	}

	/**
	 * Index Page for this controller.
	 */
	public function index() {
		$this->login();
	}

	/**
	 * Login page
	 */
	public function login()
	{
		$data = $this->session->userdata('data');
		$data['error'] = $this->session->userdata('error');
		$this->session->unset_userdata('error');
		if ( !isset($data['username']))
		{
			$data = array(
				'username'	=> '',
				'password'	=> '',
				'error'	=> '',
			);
		}
		$this->load->view('account/html', array(
			'version'	=> RevSliderGlobals::SLIDER_REVISION,
			'view_html'	=> $this->load->view('account/login', $data, TRUE)
		));
	}

	/**
	 * Login action
	 */

	public function login_action()
	{
		$data = array(
			'username' => $this->input->post('username'),
			'password' => $this->input->post('password'),
		);

		if ( !empty($data['username']) && !empty($data['password']) ) {
            $this->load->model('user_model', 'user');
            $user = $this->user->login($data['username'], $data['password']);
            if ( $user ) {
                $this->session->set_userdata('user_id', $user['id']);
                redirect( 'page=revslider' );
                die();
            }
        }

        $this->session->set_userdata('data', $data);
        $this->session->set_userdata('error', __('Incorrect login details. Please try again.') );
        redirect('c=account&m=login');
        die();
	}

	/**
	 *	Logout action
	 */

	public function logout_action() {
		$this->session->unset_userdata('user_id');
		$this->session->set_userdata('error', __('You have been logged out. Bye.') );
		redirect( 'c=account&m=login' );
	}

	/**
	 * Recover password action
	 */

	public function recover_password_action() {

		$data = array(
			'email' => $this->input->post('email')
		);
		$this->load->model('user_model', 'user');
		$user = $this->user->check_email($data['email']);
		if ( $user ) {

            $this->load->library('SaltCellar');
            $token = SaltCellar::getSalt();

            $this->load->model('option_model', 'Option');
            $tokenData = array(
                'id' => $user['id'],
                'token' => $token,
                'date' => time()
            );
            $this->Option->update_option('password_reset_token', $tokenData);

		    $resetUrl = site_url('c=account&m=reset_password&token=' . urlencode($token));

			$this->load->library('email');
			$this->email->from('mail@' . $this->input->server('server_name'), __('Slider Revolution') );
			$this->email->to($user['email']);
			$this->email->subject( __('Slider Revolution password recovery service') );
			$this->email->message( __('Open this url or copy/paste it to new browser tab to reset your password: ') . $resetUrl);
			$this->email->send();

			$this->session->set_userdata('error', __('Password reset link been sent to your email.'));
			redirect( 'c=account&m=login');
		} else {
			$this->session->set_userdata('error', __('No user exists with this email. Please try again.') );
			redirect( 'c=account&m=recover_password');
		}
	}

	/**
	 * Recover password page
	 */
	public function recover_password() {
		$data = array(
			'email'	=> '',
			'error' => $this->session->userdata('error')
		);
		$this->session->unset_userdata('error');
		$this->load->view('account/html', array(
			'version'	=> RevSliderGlobals::SLIDER_REVISION,
			'view_html'	=> $this->load->view('account/recover_password', $data, TRUE)
		));
    }

	/**
     * Reset password action
     */

	public function reset_password() {

        $this->load->model('option_model', 'Option');
        $tokenData = unserialize($this->Option->get_option('password_reset_token'));

        if (isset($tokenData['token']) && $tokenData['token'] == $this->input->get('token')) {

            $this->load->library('SaltCellar');
            $password = SaltCellar::getSalt(8, 16);

            $this->load->model('user_model', 'User');
            $user  = $this->User->get($tokenData['id']);
            $user['user_id'] = $user['id'];
            $user['password'] = $password;
            $user['confirm_password'] = $password;
            $this->User->update($user);

            $this->load->library('email');
            $this->email->from('mail@' . $this->input->server('server_name'), __('Slider Revolution') );
            $this->email->to($user['email']);
            $this->email->subject( __('Slider Revolution password recovery service') );
            $this->email->message( __('Your new password is: ') . $password);
            $this->email->send();

            $this->Option->update_option('password_reset_token', false);

            $this->session->set_userdata('error', __('New password have been sent to your email.'));
        } else {
            $this->session->set_userdata('error', __('Invalid or expired token used.') );
        }

        redirect( 'c=account&m=login');
    }

}
