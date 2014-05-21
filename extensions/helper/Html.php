<?php

namespace li3_fieldwork\extensions\helper;

class Html extends \lithium\template\helper\Html {

	public function paras($string) {
		return str_replace("\n", "</p>\n<p>", '<p>' . $string . '</p>');
	}

    public function urlsToLinks($string) {
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        if (preg_match($reg_exUrl, $string, $url)) {
            return  preg_replace($reg_exUrl, '<a href="' . $url[0] . '">' . $url[0] . '</a> ', $string);
        }
        return $string;
    }

    public function dataAttrs($data) {
        foreach ($data as $key => &$val) {
            if (is_array($val) || is_object($val)) {
                $val = json_encode($val);
            }
            $val = 'data-' . $key . '="' . $val . '"';
        }

        return implode(' ', $data);
    }

}

?>