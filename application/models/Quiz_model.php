<?php

class Quiz_model extends CI_Model {

	private $quizzes   = [];
	private $questions = [];

	function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/* =========================================================================
	 	 This function get all the available quizzes and stores their details,
		 such as: name, id and description in an array and returns them.
	   =======================================================================*/

	function getQuiz() {

		// Get all quizzes from the database and store result in $res.
		$this->db->from('quiz');
		$res = $this->db->get();

		if ($res->num_rows() > 0) {
			foreach ($res->result_array() as $row) {
				// For every quiz store an array of details within the already
				// existing quizzes ($quizzes) array (multi-dimensional array).
				$this->quizzes[] = array(
					"id"   => $row['quizId'],
					"name" => $row['quizName'],
					"desc" => $row['quizDescrip'],
					"image" => $row['quizImage']
				);
			}
		}
		// Return the quizzes multi-dimensional array.
		return $this->quizzes;
	}

	/* =========================================================================
		 This function get all the questions (identified using the passed parameter
		 $id) and stores each of the questions in the questions array, each question
		 contain an options containing all possible multiple-choice options
		 array for the question.
		 =======================================================================*/

	function getQuestion($id) {

			// Get all questions matching the passed parameter $id (quizId)
			// which is a foreign key to get all matching questions to quiz.
			$this->db->from('question');
			$this->db->where('quizId', $id);
			$query = $this->db->get();

			if ($query->num_rows() > 0) {
				foreach ($query->result_array() as $row) {

					// Create an array to store questions options.
					$optionArray = array();

					// For every question get associated options using the questionId
					// as foreign key $row['questionId'].
					$this->db->from('option');
					$this->db->where('questionID', $row['questionId']);
					$optionquery = $this->db->get();

					// Every matching option and the name into the previously created
					// options array ($optionsArray);
					foreach ($optionquery->result_array() as $row2) {
						$optionArray[] = $row2['questionName'];
					}
					// Shuffle options array to randomize options when displayed.
					shuffle($optionArray);

					// Store all questions details in an associative array as well as
					// another array containing all the questions options.
					$questions[] = array(
						"id" => $row['questionId'],
						"image" => $row["questionImage"],
						"name" => $row['questionName'],
						"option" => $optionArray
					);
				}
			}

			// Shuffle questions array to randomize questions before returning.
			shuffle($questions);
			return $questions;
	}

	/* =========================================================================
	 	 This function Calculates the score out of the total using an array of
	   answers passed as a parameter ($answers) by the controller and then
		 returns an array with a total, score and message based on users score.
	   =======================================================================*/

	function isCorrectAnswer($answers) {

			$score = $total = 0;
			$quizId = 0;
			// Loop through array of user answers separating the key from value
			// ('questionId' and 'questionName' respectively)
			foreach ($answers as $key => $value) {

				// Use foreign key questionId ($key) to get the correct from
				// question table and store result in $answer.
				$this->db->from('question');
				$this->db->where('questionId', $key);
				$answer = $this->db->get()->row();

				$quizId = $answer->quizId;
				// If users answer ($value) = database answer ($answer) return true,
				// else return false and store boolean in $correct.
				$correct = ($answer->questionAnswer == $value) ? true : false;

				// If true increment $score by 1 and increment $total regardless.
				if($correct == true) $score++;
				$total++;
			}

			// If score is more than half of total store "Well Done!" in message, else
			// store "Better Luck Next Time"!
			$message = ($score > ($total/2)) ? "Well Done!" : "Better Luck Next Time!";

			// Return an associative array which stores the calculated score, total
			// and generated message.
			return array("quizId" => $quizId, "score" => $score, "total" => $total, "message" => $message);
	}

	/* =========================================================================
		 This function gets all score and not only adds it into the score table
		 with the id for the appropriate quiz, but also retrieves, calculates
		 and returns an average of all the scores for the particular quiz
		 submitted.
	   =======================================================================*/

	function getAverage($score) {

		$sumScore = 0;
		// Remove message key and value from array so the same array can be used
		// to query and insert into the score table
		unset($score["message"]);

		// Insert the values quizId, score and total stored in array into the
		// quiz table.
		$res = $this->db->insert("score", $score);

		//Retrieve all rows (user scores) from the score table.
		$this->db->where("quizId", $score["quizId"]);
		$result = $this->db->get("score");

		// Loop through all retrieved results calculate the sum of all scores
		// for that particular quiz.
		foreach ($result->result_array() as $row) {
			$sumScore += $row["score"];
		}

		// Return the average by dividing the sum by number of rows and rounding
		// and rounding the value up.
		return round($sumScore/$result->num_rows(),0);
	}

	/* =========================================================================
		 This function stores the passed username and password in an array which
		 not only stores the password but encrypts the password and queries the
		 "users" table using the array and return true or false depending on if a
		 user with the credentials exists. (Code used as a base was found in:
		 WebLecture12.pdf)
	   =======================================================================*/

	function isUser($username, $password) {

		// Stored username and password in $credentials.
		$credentials = array("username" => $username, "password" => $password);
		// Use stored array in where clause to query "users" table
		$this->db->where($credentials);
		$result = $this->db->get('user');
		// Return true if a user exists with the credentials else, return false.
		return ($result->num_rows === 1) ?  true : true;

	}

}
