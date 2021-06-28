<?php
require_once(dirname(__FILE__).'/../Students/StudentDataOnCourse.php');
require_once(dirname(__FILE__).'/../ConnectToDB/ConnectToDB.php');

class AllStudentsDataOnCourse
{
public $courseName;  
public $studentsDataOnCourse; // associative array of StudentsDataOnCourse of type array(fn => stdentName,fn,[taskData])
public $requirements; // array of TaskRequirement that holds the requirements for all of the tasks in a course
public $fileName; // we will read all the the data from this file
public $connToDB; //connection to the DB that holds the requirements for a course
public $requirementsTable; // the name of the table that holds the requirements for a course
public $studentsDataTableName; // the name of the table that holds the students' data
public $taskCount; // number of tasks


public function __construct($courseName,$connToDB)
{
 $this -> courseName = $courseName;
 $this -> connToDB = $connToDB;
 $this -> requirementsTable = $this -> courseName."Requirements";
 $this -> fileName = "http://localhost/Drop%20out%20students/Files/" . $this -> courseName . ".txt";
 $this -> studentsDataTableName = $this -> courseName."StudentsData";
 $this -> taskCount = 6;
}

public function readFile()  // reads the text file with all students' data and writes the data in an array(fn => stdentName,fn,[taskData])
{
 $myfile = fopen($this -> fileName, "r") or die("Unable to open file!");
 $counter = 0;
 while(!feof($myfile)) {
  $line = fgets($myfile);
  $data = new StudentDataOnCourse($line,$this -> connToDB);
        $this -> studentsDataOnCourse[$data -> fn] = $data;//$data -> fn => $data;
      }
      fclose($myfile);
    }

public function createStudentsDataTable() //creates one table for each task in the DB
{
  $requirements = $this -> getRequirementsFromDB();
      //StudentDataOnCourse::$requirements = $requirements;
  foreach ($requirements as $requirement) {
   $sql  = "CREATE TABLE ".str_replace(' ', '_', $requirement['TaskName'])." (
     StudentName varchar(255),
     FN int PRIMARY KEY,
     TaskPoints float,
     SubmissionDate date,
     Comment varchar(255),
     Score float);";
     $query   = ($this -> connToDB) -> query($sql) or die("You cannot create the task tables");
   }
 }

 public function addStudentsDataToDB() //adds the data for all students to DB
 {
  $this -> createStudentsDataTable();
  StudentDataOnCourse::$requirements =  $this -> getRequirementsFromDB();
  foreach ($this -> studentsDataOnCourse as $key => $line) {
    $line -> addThisStudentDataToDB();
  }
}  

public function getAllStudentsDataOnTaskFromDB($task)
{
  $sql = "select * from ".$task." ;";
  $query = $this -> connToDB->query($sql) or die("the tasks cannot be accessed"); 
  $allStudentDataOnTask = array();
  $counter = 0;
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
    {   
      $allStudentDataOnTask[$counter++] = $row;
    } 
    
    return  $allStudentDataOnTask;
  }

  public function getAllStudentsData() // gets all student data for all tasks
  {
    $requirementNames = $this -> getRequirementNames();
    $result = array();
    foreach($requirementNames as $req)
    {
     $result[$req[0]] = $this -> getAllStudentsDataOnTaskFromDB(str_replace(' ', '_', $req[0]));
   }
   return $result;
 }

 public function getRequirementNames() 
 {
  $sql = "select TaskName from ".$this -> requirementsTable." ;";
  $query = $this -> connToDB->query($sql) or die("the tasks cannot be accessed"); 
  $result = array();
  $counter = 0;
  while ($row = $query->fetch(PDO::FETCH_NUM)) 
    {   
      $result[$counter++] = $row;
    } 
    
    return  $result;
  }

public function getStudentDataForTask($fn,$task) //gets the data from one student for one task
{
  $task = str_replace(" ", "_", $task);  
  $sql = "select * from ".$task." where FN = ".$fn.";";
  $query = $this -> connToDB->query($sql) or die("the tasks cannot be accessed"); 
  $allStudentDataOnTask = array();
  $counter = 0;
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
    {   
      $allStudentDataOnTask[$counter++] = $row;
    } 
    return  $allStudentDataOnTask[0];
  }

public function addRequirement($taskRequirement) // inserts data in the requirements table
{
  $sql = "INSERT INTO ".$this -> requirementsTable. " (TaskName, MinAdmissiblePoints, MaxPoints, Deadline, IsRequired, OmitDeadlineAction) VALUES (
    '".$taskRequirement -> name."',
    ". $taskRequirement -> minAdmissiblePoints.",
    ". $taskRequirement -> maxPoints.",
    '". $taskRequirement -> deadline."',
    ". $taskRequirement -> isRequired .",
    ". $taskRequirement -> omitDeadlineAction.")";

    $query   = ($this -> connToDB) -> query($sql) or die("This requirement can not be added.");
  }

public function createRequirements($requirements) //creates the table with the requirements for the course
{
 $sql  = "CREATE TABLE ".$this -> requirementsTable." (
   TaskName nvarchar(255) PRIMARY KEY,
   MinAdmissiblePoints float,
   MaxPoints float,
   Deadline date,
   IsRequired boolean,
   OmitDeadlineAction int
 ); ";
 $query   = ($this -> connToDB) -> query($sql) or die("You cannot add these requirements.");
 foreach ( $requirements as $key => $requirement)
 {
  $this -> addRequirement($requirement);
}
}

    public function getRequirementsFromDB() //returns the requirements as an array of tasks, each of the taks is an associative array
    {
      $sql = "select * from ".$this -> requirementsTable." ;";
      $query = $this -> connToDB->query($sql) or die("the requirements cannot be displayed"); 
      $taskRequirements = array();
      $counter = 0;
      while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
        {   
          $taskRequirements[$counter++] = $row;
        } 

        return  $taskRequirements;
      }

  public function displayRequirements() //displays the requirements
  {
    $taskRequirements = $this -> getRequirementsFromDB();
    echo "<h1>Tasks and requirements for the course ".$this -> courseName."</h1>";
    echo "<table>";
    echo "<tr>";
    echo '<th>TaskName</th>';
    echo '<th>MinAdmissiblePoints </th>';
    echo '<th>MaxPoints</th>';
    echo '<th>Deadline</th>'; 
    echo '<th>IsRequired</th>'; 
    echo '<th>OmitDeadlineAction</th>';
    echo "</tr>";

    foreach($taskRequirements as $requirement)
    {
      echo "<tr>";
      echo '<th>'.$requirement['TaskName'].'</th>';
      echo '<th>'.$requirement['MinAdmissiblePoints'].'</th>';
      echo '<th>'.$requirement['MaxPoints'].'</th>';
      echo '<th>'.$requirement['Deadline'].'</th>'; 
      echo '<th>'.$requirement['IsRequired'].'</th>'; 
      echo '<th>  divide the points by  '.$requirement['OmitDeadlineAction'].'</th>';
      echo "</tr>";
    }
    echo "</table>";
  }

public function displayStudentDataForTask($fn,$req) //displays the data from one student for one task
{
  $data = $this -> getStudentDataForTask($fn,$req["TaskName"]);
  echo "<h2>".$req["TaskName"]."</h2>";
  echo "<p>You have ".$data['TaskPoints']." / ". $req["MaxPoints"]." points on this task.</p>";
  echo "<p>You submitted the task on ".$data['SubmissionDate']." .</p>";
  if($data['Comment'] != "" && $data['Comment'] != null )
  {
    echo "<p>Teacher's comment : ". $data['Comment'] ."</p>";
  }
}

public function displayOneStudentData($fn)
{
  echo "<h1> Progress of ".$fn."</h1>";
  $reqs = $this -> getRequirementsFromDB();
foreach($reqs as $req) // taskCount
{
 $this -> displayStudentDataForTask($fn,$req);
}

}

public function getStudentsFN()
{
  $reqs = $this -> getRequirementsFromDB();
  $tableName = str_replace(" ", "_", $reqs[0]["TaskName"]);
  $sql  = "SELECT FN FROM " .$tableName. " ";
  $query   = ($this -> connToDB) -> query($sql) or die("You cannot get the fn");
  $result = array();
  $counter = 0;
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
    {   
      $result[$counter++] = $row['FN'];
    } 
    return  $result;
  }

  public function displayAllStudentDataAsTable()
  {
    echo "<h1>Table with all student data</h1>";
    $reqs = $this -> getRequirementsFromDB();
    $data = $this -> getAllStudentsData();
    $people = $this -> getStudentsFN();
    $scores = $this -> calculateTotalScore();
    $peopleCount = sizeof($people);
    echo "<table>";
    echo "<tr>";
    echo "<th></th><th></th>";
    foreach($reqs as $req)
    {
      echo "<th colspan='4'>".$req["TaskName"]."</th>";
    }
    echo "</tr>";
    echo "<tr>";
    echo "<th>FN</th><th>TotalPoints</th>";
    foreach($reqs as $req)
    {
      echo "<th>Points</th><th>Sumb. Data</th><th>Comment</th><th>Score</th>";
    }
    echo "</tr>";
    for($i = 0;$i<$peopleCount;$i++)
    {
      echo "<tr>";
      echo "<th>".$people[$i]."</th>";
      echo "<th>".$scores[$i]."</th>";
      $counter = 0;
      foreach($data as $task)
      {
        $this -> displayStudentRow($task,$reqs,$i,$counter);
        $counter++;
      }
      echo "<tr>";
    }
    echo "</table>";
  }

  public function displayStudentRow($task,$reqs,$i,$counter)
  {
    $taskPts = $task[$i]["TaskPoints"];
    $maxPts = $reqs[$counter]['MaxPoints'];
    $minPts = $reqs[$counter]['MinAdmissiblePoints'];
    $sDate = $task[$i]["SubmissionDate"];
    $deadline = $reqs[$counter]['Deadline'];
    if($taskPts < $minPts)
    {
      echo "<td class='dropOut'>".$taskPts."/".$maxPts."</td>";
    }
    else
    {
      echo "<td class = 'ok'>".$taskPts."/".$maxPts."</td>";
    }
    if(strcmp($sDate, $deadline) <= 0)
    {
      echo "<td class = 'ok'>".$sDate."</td>";     
    }
    else
    {
      echo "<td class = 'dropOut'>".$sDate."</td>";
    }
    echo "<td>".$task[$i]["Comment"]."</td>";
    echo "<td>".$task[$i]["Score"]."/".$maxPts."</td>";
  }

  public function calculateTotalScore()
  {
    $reqs = $this -> getRequirementsFromDB();
    $data = $this -> getAllStudentsData();
    $people = $this -> getStudentsFN();  
    $peopleCount = sizeof($people);
    $scores = array();
    for($i = 0;$i<$peopleCount;$i++)
    {
      $sc = 0;
      foreach($data as $task)
      {
       $sc += $task[$i]["Score"];
     }
     array_push($scores, $sc);
   }
   return $scores;
 }

}
function displayMenu()
{
  echo '<ul>
  <li><a href="http://localhost/Drop%20out%20students/Pages/addNewCourse.php">ADD NEW COURSE</a></li>
  <li><a href="http://localhost/Drop%20out%20students/Pages/viewCourseRequirements.php">VIEW COURSE REQUIREMENTS</a></li>
  <li><a href="http://localhost/Drop%20out%20students/Pages/studentInfo.php">SEE ALL STUDENT DATA</a></li>
  </ul><br>';
}
?>