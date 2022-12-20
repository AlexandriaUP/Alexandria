<?php
class Role {
	public $id, $name, $order_id;
	function __construct($id, $name, $order_id) {
		$this->id = $id;
		$this->name = $name;
    $this->orderId = $order_id;
	}
}
?>
