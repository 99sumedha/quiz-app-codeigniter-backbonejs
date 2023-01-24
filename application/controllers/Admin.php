<?php

class Admin extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('Quiz_model');
		$this->load->library('session');
		$this->load->helper('url');

		// Prevent caching of page
		// button after logout (Making sure a web page is not cached, 2016).
		header("Cache-Control: no-cache, no-store, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: 0");
	}

	/* =========================================================================
		 This index function will load automatically upon the controller being
		 called, it will check if the session is set (indicating a user is logged
		 in). If the session is not empty the Admin View will be loaded, but if it
		 is empty the user will be redirected to the Quiz Controller which will
		 load the home page. 
		 =======================================================================*/

	public function index() {
		// Store session data in $session["username"].
		$session["username"] = $this->session->userdata('username');

		// If it is empty the user is not logged in, as such redirect to homeview,
		// else load adminview.
		(!empty($session["username"]))
		? $this->load->view("adminview", $session)
		: redirect('/Quiz/quiz');
	}

}
