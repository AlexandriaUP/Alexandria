<?php
class FacultyMember {
	public $id, $first_name, $last_name, $scholar_id, $scopus_id, $department, $rank, $role, $phd_year, $isValidated;
	function __construct($id, $first_name, $last_name, $scholar_id, $scopus_id, $department, $rank, $role, $phd_year, $isValidated) {
		$this->id = $id;
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->scholar_id = $scholar_id;
		$this->scopus_id = $scopus_id;
		$this->department = $department;
		$this->rank = $rank;
		$this->role = $role;
		$this->phd_year = $phd_year;
		$this->isValidated = $isValidated;
	}
}
?>
