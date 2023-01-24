<!DOCTYPE html>
<HTML lang="en">
<HEAD>
	<meta charset="UTF-8">
	<title>QuizApp</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="/CodeIgniter/assets/css/style.css"/>
</HEAD>

<BODY>

	<!-- Login Modal Window -->
	<div class="md-modal">
		<div class="md-content">
			<header><h1>Login</h1></header>
			<form>
        <div class="input-contain">
          <input id="username" type="text">
          <label>Username</label>
        </div>
        <div class="input-contain">
          <input id="password" type="password">
          <label>Password</label>
        </div>
        <button id="loginBtn">Sign In</button>
      </form>
			<div class="md-error"></div>
			<button id="md-close">✖</div>
    </div>
  </div>

	<!-- Display header with centered logo. -->
	<div class="nav-lockup">
		<div class="center">
			<a href="quiz" class="logo"></a>
			<input id="openLogin" type="submit" value="login">
		</div>
	</div>

	<!-- Display a header with a welcome statement and brief instructions. -->
	<h1>Are you itching to take a quiz? Choose from the selection below and get started!</h1>

	<!-- A grid containing all the available quizzes. -->
	<div class="quiz-grid">

		<!-- Open a loop to loop through passed array named $quizzes and each row is named $quiz. -->
		<?php foreach ($quizzes as $quiz) { ?>

		<div class="grid">
			<div class="grid-img">
				<!-- Display image by stating path and adding the file name stored in the database by echoing
				the associative array key "image" after the path to the folder which holds the image name. -->
				<img src="/CodeIgniter/assets/img/quiz/<?php echo $quiz['image'] ?>" alt="Quiz Image">
			</div>

			<!-- Display the name of the quiz in a h3 tag by using the associative array key 'name' which
			stores the name of the particular quiz, the same applies to the description which has key of
			'desc' and is stored within a p tag. -->
			<h3><?php echo $quiz['name'] ?></h3>
			<p><?php echo $quiz['desc'] ?></p>

			<!-- Call the questions function which displays the quizzes question and append the id to the
			url using get with a key of id by echo the quiz id at the end of the url 'questions?id=' -->
			<a href="questions?id=<?php echo $quiz['id'] ?>" class="cta-grid">Take Quiz</a>

		</div>
		<!-- Close loop. -->
		<?php } ?>
	</div>

	<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>

	<script>

		/* jQuery code for login modal window. */
		$("#openLogin").on("click", function(e) {
			e.preventDefault();
			$(".md-modal").addClass("show");
			$("body").addClass("md-open");
			$(".md-error").html("")

		});

		$("#md-close").on("click", function(e) {
			$(".md-modal").removeClass("show");
			$("body").removeClass("md-open");
		});

		$("form input").on("blur", function() {
			if($(this).val() != "") {
				$(this).addClass("is-fill")
			} else {
				$(this).removeClass("is-fill")

			}
		});

		$("#loginBtn").on("click", function(e) {
			// The username and password have been retrieved from the form using .val() and store them in the variables "user" and "pass".
			e.preventDefault();
			var user = $("#username").val();
			var pass = $("#password").val();

			// The stored username and password have been stored in a javascript object named "data" so they can easily be passed using ajax.
			var data = {
				username: user,
				password: pass,
			};


			$.ajax({
				url: "http://localhost/CodeIgniter/index.php/Authenticate/login", // Send the form data to the login function in the authenticate controller.
				method: 'POST', // Send data using POST request.
				data: data, // Pass username and password stored in data.
				dataType: 'json', // Expecting JSON to be returned
				success: function(data) {

					// If the results of data contains true (user is allowed logged in), then the user will be redirected to the
					// Admin panel, however if it false an error message will be added to the login popup/modal.
					if(data) {
						window.location.href="http://localhost/CodeIgniter/index.php/Admin"
					} else {
						$(".md-error").html("Your username or password are incorrect.")
					}

				}
			});


		});

	</script>

</body>
</html>
