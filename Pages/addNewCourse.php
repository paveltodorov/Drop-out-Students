<!DOCTYPE html>  
<link rel="stylesheet" type="text/css" href="../CSS/style.css">
<html>  
<body>  
	<meta charset="UTF-8">
	<?php 
	require_once(dirname(__FILE__).'/../Students/AllStudentsDataOnCourse.php');
	displayMenu(); 
	?>
	<form id="addCourseForm" method="POST" action="http://localhost/Drop%20out%20students/Pages/registerRequirements.php">
		<fieldset>
			<label for="courseName">Course Name: </label> 
			<input type="text" name="courseName" id="courseName" size="15"><br><br>
			<?php
			echo showForm(6);
			?>
			<input type="submit" id = "submit" value="Submit">  
		</fieldset>
	</form>  


</body>
<?php
function showForm($num)
{
	$res = "";
	for($i = 0;$i<$num;$i++)
	{
		$form = 
		'<label for="taskName">Task Name: </label> 
		<input type="text" name="taskName'.$i.'" id="taskName'.$i.'" size="15" value="Task'.$i.'">

		<label for="minAdmissiblePointse">Min Admissible Points: </label> 
		<input type="number" name="minAdmissiblePoints'.$i.'" id="minAdmissiblePoints'.$i.'" size="5" value=0>

		<label for="maxPoints">Max Points: </label> 
		<input type="number" name="maxPoints'.$i.'" id="maxPoints'.$i.'" size="5" value=100>

		<label for="deadline">Deadline: </label> 
		<input type="text" name="deadline'.$i.'" id="deadline'.$i.'" size="10" value="2017-01-01">

		<label for="isRequired">Is it required? </label> 
		<input type="number" name="isRequired'.$i.'" id="isRequired'.$i.'" size="2" value=1>

		<label for="omitDeadlineAction">Omit Deadline Action: </label> 
		<input type="number" name="omitDeadlineAction'.$i.'" id="omitDeadlineAction'.$i.'" size="5" value=1> <br>';
		
		$res = $res.$form;
	}
	return $res;
}

?>

</html>