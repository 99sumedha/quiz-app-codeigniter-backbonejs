<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>QuizApp</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="/CodeIgniter/assets/css/style.css"/>
	</head>

	<body>
		<!-- Display header with centered logo. -->
		<div class="nav-lockup">
			<div class="center">
				<a href="quiz" class="logo"></a>
			</div>
		</div>

		<div class="container">
			<!-- Create a form that on submit sends data to the results function in the controller by using the
			POST method. -->
			<form action="results" method="post">

				<!-- Loop through $questions array and each row (question) will be accessed using $question. -->
				<?php foreach ($questions as $question) { ?>
				<div class="question-lockup">
					<div>
						<!-- Display the question name using it's key ('name') in the $question associative array. -->
						<h3><?php echo $question['name'] ?></h3>

						<!-- Loop through the options array within the $question array using it's key 'option' with each
						individual option being $option. -->
						<?php foreach ($question['option'] as $option) { ?>

						<!-- for each option create a radio button using the question key 'id' as the input name and
						so each questions options are linked together. Also, the value will be retrieved using $option which contains
						the name of the question and these will be echoed in the value and within the displayed in the label. -->
						<input type="radio" id="radio<?php echo $option ?>" name="<?php echo $question['id']?>" value="<?php echo $option ?>" required>
						<label for="radio<?php echo $option ?>"><?php echo $option ?></label>
						<!-- Close option loop. -->
						<?php } ?>
					</div>

					<!-- Set image retrieved from database as  a background image by using the image name stored within the $qestion array
					with the key 'image' by concatenating it with the folder path from the root directory. -->
					<div class="img-container" style="background-image: url('/CodeIgniter/assets/img/questions/<?php echo $question["image"] ?>')"></div>

				</div>
				<!-- Close question loop -->
				<?php } ?>

				<!-- On submission the answers shall be passed in an array to the result function using POST method with the questionId
				becoming the key to the selected answer, these can then be split up and used to query the database. -->
				<input class="btn" type="submit" value="Submit">

			</form>
		</div>
	</body>
</html>
