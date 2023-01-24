<?php

class Quiz extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('Quiz_model');
	}

	/* =========================================================================
		 The quiz function will load the first view when the controller
		 is called in our case it loads the home view and displays all available
		 quizzes.
		 =======================================================================*/

	function quiz() {
		// Get available quizzes from the model and store the returned array in the
		// array $quizindex which have 'quizzes' as their key.
		$quizindex['quizzes'] = $this->Quiz_model->getQuiz();
		// Send the quiz array $quizindex to the view and display the home view
		$this->load->view('homeview', $quizindex);
	}

	/* =========================================================================
		 The questions function will display all the questions and their options
		 that are linked to the quiz selected by the user (achieved by passing the
		 quizId appended to the url).
		 =======================================================================*/

	function questions() {
		// Get id appended to url with key 'id' using GET, then store within the
		// variable $quizID.
		$quizID =  $this->input->get('id', TRUE);
		// Send quizID as a parameter and get an array of questions which are then
		// stored within $newquestion;
		$newquestion['questions'] = $this->Quiz_model->getQuestion($quizID);
		// Send questions to view and display Question View as an associative array
		// with a key of "questions" and then display questionview.
		$this->load->view('questionview',$newquestion);
	}

	/* =========================================================================
		 The results array retrieves the posted array and if it exists it sends
		 the array containing the selected options and the question id to the
		 model which then calculates the score, total and message and returns the
		 array containing these values. This is then stored in the $res which
		 is then passed to the view to be displayed.

		 Update: results has been modified to not only calculate and return
		 score, total and message to the 'scoreview', but also return the average
		 score based on all the previous attemps. $res and $average are both
		 passed to the 'scoreview' by being stored in the array $data.
		 =======================================================================*/

	function results() {
			// Initilise $res (results) array & get posted array and store in $post.
			$res  = array();
			$post = $this->input->post();
			// If post array is set get results by passing the posted array as a
			// parameter to the models function 'isCorrectAnswer' which returns an
			// array cont score, message and total which are then stored in $res.
			if(!empty($post)) $res = $this->Quiz_model->isCorrectAnswer($post);

			// Pass the returned $res to the 'getAverage' method which calculates
			// and returns the average of all the scores for the quiz being taken.
			$average = $this->Quiz_model->getAverage($res);

			// Store both the $res and $average in a $data array with the appropriate
			// keys so they can both be sent to the score view.
			$data = array();
			$data["score"] = $res;
			$data["average"] = $average;

			// Load the score view to display results and send $data.
			$this->load->view('scoreview', $data);
	}

}
