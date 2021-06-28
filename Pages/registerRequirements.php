<html>
<body>
	<link rel="stylesheet" type="text/css" href="../CSS/style.css">

	<php displayMenu(); ?>
		<p>You added the requirements successfully.</p>
		<?php
		require_once(dirname(__FILE__).'/../Students/AllStudentsDataOnCourse.php');
		displayMenu();
		$connectToDB = new ConnectToDB();
		$conn = $connectToDB -> connToDB or die ("failed");
		$data = new AllStudentsDataOnCourse($_POST["courseName"],$conn);
//var_dump($_POST);
		$taskRequirement = array();
		$reqCount = sizeof($_POST)/5;
		for($i = 0;$i<6;$i++)
		{
			$taskName = $_POST['taskName'.$i];
			$minPts = $_POST['minAdmissiblePoints'.$i];
			$maxPts = $_POST['maxPoints'.$i];
			$deadline = $_POST['deadline'.$i];
			$isRequired = $_POST['isRequired'.$i];
			$omit = $_POST['omitDeadlineAction'.$i];
			$taskRequirement[$i] = new TaskRequirement($taskName,$minPts,$maxPts,$deadline,$isRequired,$omit);
		}
		$data -> createRequirements($taskRequirement);
		$data -> readFile();
		$data -> addStudentsDataToDB();
?>
</body>
</html>