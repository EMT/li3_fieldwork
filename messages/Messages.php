<?php

namespace fieldwork\messages;

use lithium\storage\Session;


class Messages {

	
	private static $_messages = array();
	private static $_rendered = false;
	
	
	public static function add(array $message, array $options = array()) {
		static::$_messages[] = $message;
	}
	
	
	public static function get() {
		return array_merge(static::$_messages, ((Session::read('fw_messages')) ?: []));
	}
	
	
	public static function save() {
		Session::write('fw_messages', static::$_messages);
	}
	
	
	public static function clear() {
		Session::delete('fw_messages');
	}
	
	
	public static function setRenderedFlag() {
		static::$_rendered = true;
	}
	
	
	public static function rendered() {
		return static::$_rendered;
	}

}



?>