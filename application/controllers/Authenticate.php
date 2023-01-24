<?php

class Authenticate extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('Quiz_model');
		$this->load->library('session');
		$this->load->helper('url');

	}

	/* =========================================================================
		 The login fuction will retrieve the posted superglobal variables (via
		 AJAX) and store them in $username and $password. These variables and
		 their contents will then be sent to the "isUser" method within the model
		 which will return a value based on if the credentials exist within the
		 database.
		 =======================================================================*/

	public function login() {

		// Retrieve posted data and pass is to the isUser function which returns
		// returns the array of results if the username and password combination exist
		// within the database, this returned value is stored in $res.
		$username = $this->input->post("username");
		$password = $this->input->post("password");
		$res = $this->Quiz_model->isUser($username, $password);

		// If the contents of $res are not empty it means the user exists as such the
		// session data containing the users username is created signifying the user
		// is logged in.
		if($res) $this->session->set_userdata(array("username" => $username));

		// Echo the JSON of the return of $res (true or false)
		// echo json_encode($res);
		echo json_encode($res);

	}

	/* =========================================================================
		 The logout function will destory the session variables (used to indicate)
		 whether a user is logged in) and redirect the user to the quizzes home
		 page.
		 =======================================================================*/

	public function logout() {
		// Destroy the session and its variables
		$this->session->sess_destroy();
		// Redirect user to homeview/
		redirect('/Quiz/quiz');
	}

}
