<?php
class Department {
	public $id, $name, $school;
	function __construct($id, $name, $school) {
		$this->id = $id;
		$this->name = $name;
		$this->school = $school;
	}
}
?>
