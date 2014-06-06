<?php

namespace li3_fieldwork\utils;


class Url {

    public static function appUrl() {
        $url = $pageURL = 'http';
        $url .= (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on') ? 's' : '';
        $url .= '://' . $_SERVER["SERVER_NAME"];
        return $url;
    }
}


?>