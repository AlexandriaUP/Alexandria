<?php
class Metric {
	public $publications, $citations, $hindex, $i10index, $mindex, $most_paper_citations;
	function __construct($publications, $citations, $hindex, $i10index) {
		$this->publications = $publications;
		$this->citations = $citations;
    	$this->hindex = $hindex;
    	$this->i10index = $i10index;
	}
}
?>
