<?php
class TaskRequirement
{
public $name;
public $minAdmissiblePoints;
public $maxPoints;
public $deadline;
public $isRequired;
public $omitDeadlineAction;

public function __construct($name, $minAdmissiblePoints,$maxPoints,$deadline,$isRequired,$omitDeadlineAction)
  {
   $this -> name = $name;
   $this -> minAdmissiblePoints = $minAdmissiblePoints;
   $this -> maxPoints = $maxPoints;
   $this -> deadline = $deadline;
   $this -> isRequired = $isRequired;
   $this -> omitDeadlineAction = $omitDeadlineAction;
  }
}
?>