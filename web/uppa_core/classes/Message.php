<?php
class Message {
	public $type, $content;
	function __construct($type, $content) {
		$this->type = $type;
		$this->content = $content;
	}
}
?>
