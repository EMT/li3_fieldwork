<?php

namespace fieldwork\extensions\helper;

class String extends \lithium\template\Helper {

	public function truncate($string, $length, $last_word = false) {
		if (strlen($string) > $length) {
			if ($last_word) {
				return substr($string, 0, strpos(wordwrap($string, $length), "\n")) . '…';
			}
			return substr($string, 0, $length) . '…';
		}
		return $string;
	}

}

?>