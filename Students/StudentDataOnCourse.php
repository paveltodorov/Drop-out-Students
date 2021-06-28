<?php
require_once(dirname(__FILE__).'/../Tasks/TaskData.php');
require_once(dirname(__FILE__).'/../Tasks/TaskRequirement.php');

class StudentDataOnCourse // holds and manages the data for one student for one course
{
public $studentName;
public $fn;
public $taskData; // array of TaskData
public $taskCount; // we take it from the DB
public $connToDB; //connection to the DB that holds the requirements for a course
public static $requirements; // requirements for a task - they are the same for each student

public function parseLine($line) //reads the task data for one student
{
 $data = explode(",", $line);
 $this -> studentName = $data[0];
 $this -> fn = $data[1];
 $counter = 0;
 for($i = 2;$i<$this -> taskCount*3 + 2;$i+=3){
  $this -> taskData[$counter++] = new TaskData((float)$data[$i],$data[$i + 1],$data[$i + 2]);
}
}

public function __construct($line,$connToDB)
{
  $this -> taskCount = 6; 
  $this -> parseLine($line);
  $this -> connToDB = $connToDB;
}

public function calculateScoreByIndex($index)
{
  $task = $this -> taskData[$index];
  $req = $this::$requirements[$index];

 $score =  ($task -> taskPoints); // ($req['MaxPoints']);
 if( strcmp($task -> taskPoints, $req['Deadline']) > 0)
 {
   if( ($req["OmitDeadlineAction"]) != 0  ) 
   {
     $score = $score/($req["OmitDeadlineAction"]);
   }
   else 
   {
    $score = 0;
  }
}
return $score;
}

public function addThisStudentDataToDB() //adds the data for this particular student to DB
{
  for($i = 0;$i < $this -> taskCount;$i++)
  {
    $sql = "INSERT INTO ". str_replace(' ', '_', $this::$requirements[$i]['TaskName'])." (StudentName, FN, TaskPoints, SubmissionDate, Comment, Score) VALUES (
      '".$this -> studentName."',
      ". $this -> fn.",
      ". $this -> taskData[$i] -> taskPoints.",
      '". $this -> taskData[$i] -> submissionDate."',
      '".$this -> taskData[$i] -> comment."',
      ". $this -> calculateScoreByIndex($i).");";
      try {
        $query   = ($this -> connToDB) -> query($sql) or die("This  student data can not be added." );
      }
      catch(PDOException $e)
      {
       echo $sql . "<br>" . $e->getMessage();
     }
   }

 }

}
?>
