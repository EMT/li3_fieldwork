<?php

namespace li3_fieldwork\extensions\helper;

use dflydev\markdown\MarkdownParser;


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

    public function pageData($data) {
        return '<script>var pageData = ' . json_encode($data) . '</script>';
    }

    public function markdown($string) {
        require_once(__DIR__ . '/../../utils/markdown/IMarkdownParser.php');
        require_once(__DIR__ . '/../../utils/markdown/MarkdownParser.php');
        $markdown = new MarkdownParser();
        return $markdown->transformMarkdown($string);
    }

}

?>