<?php

namespace li3_fieldwork\extensions\helper;

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

    public function slugify($text) { 
        // replace non-alphanumeric chars with -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        return $text;
    }

}

?>