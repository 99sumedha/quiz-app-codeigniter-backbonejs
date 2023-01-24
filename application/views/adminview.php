<!DOCTYPE html>
<HTML lang="en">
<HEAD>
	<meta charset="UTF-8">
	<title>QuizApp</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet" type="text/css">
	<link href="/CodeIgniter/assets/css/new/main.css" rel="stylesheet" type="text/css">
</HEAD>

<BODY>

	<!-- This section conains the header, which contins the logo and a logout button. -->
	<header>
		<div class="hd-inner">
			<div class="hd-nav" ></div>
			<div class="hd-logo">
				<a href="#">quiz<b>app</b></a>
			</div>
			<div class="hd-user">
				<!-- The base url along with the path to the logout function has been added to the href of the logout link, thus on click
				     the code within the logout method will be run destroying the session and logging the admin out. -->
				<a href="<?php echo base_url() ?>/index.php/Authenticate/Logout">Logout</a>
			</div>
		</div>
	</header>

	<main class="page">
		<!-- Container for the main content, the available quizzes, questions and options shall be added to this div using both jQuery and Underscore. -->
		<div class="content"></div>
		<!-- This will be a popup modal this will contain the form used to add, edit or delete a quiz, question or option. -->
		<div class="md-modal">
			<button class="close">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 23.332 23.333" height="23.333px" id="Capa_1" version="1.1" viewBox="0 0 23.332 23.333" width="23.332px" xml:space="preserve"><path d="M16.043,11.667L22.609,5.1c0.963-0.963,0.963-2.539,0-3.502l-0.875-0.875c-0.963-0.964-2.539-0.964-3.502,0L11.666,7.29  L5.099,0.723c-0.962-0.963-2.538-0.963-3.501,0L0.722,1.598c-0.962,0.963-0.962,2.539,0,3.502l6.566,6.566l-6.566,6.567  c-0.962,0.963-0.962,2.539,0,3.501l0.876,0.875c0.963,0.963,2.539,0.963,3.501,0l6.567-6.565l6.566,6.565  c0.963,0.963,2.539,0.963,3.502,0l0.875-0.875c0.963-0.963,0.963-2.539,0-3.501L16.043,11.667z"/></svg>
			</button>
			<!-- The form will be appended to this containing div.-->
			<div class="md-content"></div>
		</div>
		<div class="overlay"></div>
	</main>


	<!--
		Below are the Underscore.js templates, these allow data to be passed from Backbone.js and manipulated. Certiain templates are
		created depending on what view is called.
	-->

	<!-- QUIZ LIST TEMPLATE START -->
	<script type="text/template" id="quiz-list-template">
		<button class="new-quiz">Add new quiz</button>
		<ul class="qu-outer">

			<!--
				An underscore foreach loop will be run here, this will use the quizzes that were passed from within the Backbone.js code, each availble quiz
				will be named "quiz". Within each iteration the quizzes name, description and image are retrieved using the "quiz.get()" which contains the keys
				value for thee details ("quizName", "quizDescrip" and "quizImage" respectively), these are then stored in the appropriate tags. Lastly, the quiz
				id is retrieved in the using the same method detailed above but is concatenated with "#/Quiz/" to be used with routers in the Backbone.js code.
			-->

			<% _.each(quizzes, function(quiz) { %>
			<li>
				<div class="qu-tile">
					<div class="qu-info">
						<a href="#" class="edit-quiz"></a>
						<input type="hidden" value='<%= quiz.get("quizId") %>'>
						<h3><%= quiz.get("quizName") %></h3>
						<span>by Sumedha Karunarathna</span>
						<p><%= quiz.get("quizDescrip") %></p>
						<a href="#/Quiz/<%= quiz.get('quizId') %>" class="viewq">View Questions</a>
					</div>
					<div class="qu-img" style="background-image: url(/CodeIgniter/assets/img/quiz/<%= quiz.get('quizImage') %>)"></div>
				</div>
			</li>
			<% }); %>
		</ul>
	</script>
	<!-- QUIZ LIST TEMPLATE END -->


	<!-- QUIZ FORM TEMPLATE START -->
	<script type="text/template" id="quiz-edit-template">

		<!--
			A form to be appended to the above modal (.md-content) will be created here, the various input fields required of a form are created anda number
			of "Underscore.js" if statements are also utilised, to differentiation between editing an existing quiz and creating a new one. As shown by the below
			(in lines such as: "<%= quiz ? quiz.get('quizDescrip') : '' %>") if the passed "quiz" object is not null it means an update is being run as such
			a quizzes appropriate details are added into the input fields, however if it is null (new quiz is being created) the input fields will be left
			blank.
		-->

		<!-- If an edit is occuring the title within the modal will be "Update Existing Quiz", else it will be "Add New Quiz" -->
		<h1><%= quiz ? "Update Existing" : "Add New" %> Quiz</h1><hr>
		<form class="quiz-form">
			<input type="text" placeholder="Enter quiz name here..." name="quizName" value="<%= quiz ? quiz.get('quizName') : '' %>">
			<input type="text" placeholder="Enter image name here..." name="quizImage" value="<%= quiz ? quiz.get('quizImage') : '' %>">
			<textarea rows="8" cols="40" placeholder="Enter quiz description..." name="quizDescrip"><%= quiz ? quiz.get('quizDescrip') : '' %></textarea>
			<button class="submit" type="submit"><%= quiz ? "Update" : "Create" %></button>
			<!-- The below code specifies that Undercore will only add the input field (to signify an update) and a delete button if an edit is occuring. -->
			<% if (quiz) { %>
				<input type="hidden" name="quizId" value="<%= quiz.id %>">
				<button class="delete" type="button">Delete</button>
			<% } %>
		</form>
		<span class="error"></span>
	</script>
	<!-- QUIZ FORM TEMPLATE END -->


	<!-- QUESTION LIST TEMPLATE START -->
	<script type="text/template" id="question-list-template">

		<!--
			Similar to the previous (shown below), the relavent details are retrieved for the questions using their key names along with the ".get()" method, as such a row
			containing a quizzes name, answer, image name along with buttons is created for each question available through the use of an Underscore loop.
		-->

		<button class="new-question">Add new question</button><!-- This button will open the modal to create a new question. -->
		<table class="question-table">
			<thead>
				<tr>
					<th>Question</th>
					<th>Answer</th>
					<th>Image</th>
					<th></th><th></th>
				</tr>
			</thead>
			<tbody>
				<!-- Below is the each loop using the passed collection for questions, each iteration will be named "question". -->
				<% _.each(questions, function(question) { %>
					<tr>
						<!-- The various details are retrieved and stored within table cells as shown below. -->
						<input type="hidden" value='<%= question.get("questionId") %>'>
						<td><%= question.get("questionName") %></td>
						<td><%= question.get("questionAnswer") %></td>
						<td><%= question.get("questionImage") %></td>
						<!-- An edit button is creation which will open the above modal to allow this chosen quiz to be edited. -->
						<td><a href='#' class='edit-question' class="btn btn-primary"></a></td>
						<!-- The "questionId" has been retrieved and appended to the url "#/Question/" to be used with backbone routers to retrieve all the options associated
								 with this question. -->
						<td><a href='#/Question/<%= question.get("questionId") %>' class="button">Options</a></td>
					</tr>
				<% }); %>
			</tbody>
		</table>
	</script>
	<!-- QUESTION LIST TEMPLATE END -->


	<!-- QUESTION FORM TEMPLATE START -->
	<script type="text/template" id="question-edit-template">

		<!--
			Similar, to the previous template a form with the appropriate input fields to create or edit a question is created. A number of Underscore if
		 	conditionals are used to decide whether to retrieve and place existing data into the input fields or to leave them blank.
		-->

		<h1><%= question ? "Update Existing" : "Add New" %> Question</h1><hr>
		<form class="question-form">
			<!--
				Below the needed input fields are created and if needed the questions details (name, answer, image and id) are retrieved using ".get()" and placed
			  into the input fields values.
			-->
			<input type="hidden" name="quizId"  value="<%= id %>">
			<input type="text" name="questionName" value="<%= question ? question.get('questionName') : '' %>" placeholder="Enter question name....">
			<input type="text" name="questionAnswer" value="<%= question ? question.get('questionAnswer') : '' %>" placeholder="Enter question answer...">
			<input type="text" name="questionImage" value="<%= question ? question.get('questionImage') : '' %>" placeholder="Enter question image...">
			<button class="submit" type="submit"><%= question ? "Update" : "Create" %></button><!-- Submit button is named depending on the edit or create procedure. -->
			<!-- The elements within thisconditional are only shown when question does not amount to null (meaning an edit of an existing question is occuring). -->
			<% if (question) { %>
				<input type="hidden" name="questionId" value="<%= question.id %>">
				<button class="delete" type="button">Delete</button>
			<% } %>
		</form>
		<span class="error"></span>
	</script>
	<!-- QUESTION FORM TEMPLATE END -->


	<!-- OPTION LIST TEMPLATE START -->
	<script type="text/template" id="option-list-template">

		<!--
			Once again the details for each option available for a question are retrieved using ".get()" along with an options key "quizName" within a underscore-min
			foreach loop. Within this loop a row is created within a table for each available option, this row contains the options name along with an "edit" button.
		-->

		<button class="new-option">Add new option</button>
		<table class="option-table">
			<thead>
				<tr>
					<th>Option</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<% _.each(options, function(option) { %>
				<tr>
					<input type="hidden" value='<%= option.get("optionID") %>'>
					<td><%= option.get("questionName") %></td>
					<td><a href="#" class="edit-option"></a></th>
				</tr>
			<% }); %>
			</tbody>
		</table>
	</script>
	<!-- OPTION LIST TEMPLATE END -->


	<!-- OPTION FORM TEMPLATE START -->
	<script type="text/template" id="option-edit-template">

		<!--
			Similar, to the other form templates a form with the appropriate input fields is created. A number of Underscore if conditionals
			are used to decide whether to retrieve and place existing data into the input fields or to leave them blank, this allows admins
			to either edit existing information or add their own new information.
		-->

		<h1><%= option ? "Update Existing" : "Add New" %> Option</h1><hr>
		<form class="option-form">
			<input type="hidden" name="questionID" value="<%= id %>">
			<input type="text" name="questionName" value="<%= option ? option.get('questionName') : '' %>">
			<button class="submit" type="submit"><%= option ? "Update" : "Create" %></button>
			<% if (option) { %>
				<input type="hidden" name="optionID" value="<%= option ? option.get('optionID') : '' %>">
				<button class="delete" type="button">Delete</button>
			<% } %>
		</form>
		<span class="error"></span>
	</script>
	<!-- OPTION FORM TEMPLATE EDIT -->


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.2.3/backbone-min.js"></script>
	<script src="/CodeIgniter/assets/js/main.js"></script>

</BODY>
</HTML>
