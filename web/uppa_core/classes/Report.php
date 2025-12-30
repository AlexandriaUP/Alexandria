<?php
class Report{
	public $id, $title, $datetimeCreated, $progress;
	function __construct($id, $title, $datetimeCreated) {
		$this->id = $id;
		$this->title = $title;
		$this->datetimeCreated = $datetimeCreated;
	}
}
?>
