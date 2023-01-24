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

		<!-- Display results in a div named .score-lockup. -->
		<div class="score-lockup">
			<div class="score">
				<!-- echo exploded array keys as variables within the heading tags with the correct count being $score["score"]
				and the total number of questions being $score["total"]. -->
				<h1>You scored: <?php echo $score["score"] ?> / <?php echo $score["total"] ?></h1>
				<!-- Also echo the exploded array keys containing the average ($average) and message
				($score["message"]). -->
				<h1>The average: <?php echo $average ?> / <?php echo $score["total"] ?></h1>
				<h2><?php echo $score["message"] ?> </h2>
				<!-- On click run the quiz function in the controller, returning to the home page with index of quizzes. -->
				<a href="quiz" class="cta-score">Return Home</a>
			</div>

		</div>
	</body>
</HTML>
