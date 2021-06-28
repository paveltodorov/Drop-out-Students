<html>
<body>
	<link rel="stylesheet" type="text/css" href="../CSS/style.css">
	<?php
	require_once(dirname(__FILE__).'/../Students/AllStudentsDataOnCourse.php');
	displayMenu();
	$connectToDB = new ConnectToDB();
	$conn = $connectToDB -> connToDB or die ("failed");
	$data = new AllStudentsDataOnCourse("WEB",$conn);
	$data -> displayAllStudentDataAsTable();
	?>
</body>
</html>