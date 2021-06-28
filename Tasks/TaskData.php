<?php
class TaskData
{
public $taskPoints;
public $submissionDate;
public $comment;

public function __construct($taskPoints,$submissionDate,$comment)
 {
  $this -> taskPoints = $taskPoints;
  $this -> submissionDate = $submissionDate;
  $this -> comment = $comment;
 }
 
}
?> 