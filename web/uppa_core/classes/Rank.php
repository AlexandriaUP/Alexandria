<?php
class Rank {
	public $id, $full_title;
	function __construct($id, $full_title, $short_title, $order_id) {
		$this->id = $id;
		$this->full_title = $full_title;
    $this->shorter_title = $short_title;
    $this->order_id = $order_id;
	}
}
?>
