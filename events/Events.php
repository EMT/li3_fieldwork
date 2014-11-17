<?php

namespace li3_fieldwork\events;


class Events {

    protected static $_listeners = [];

    public static function bind($eventName, callable $callback) {
        if (!isset(Events::$_listeners[$eventName])) {
            Events::$_listeners[$eventName] = [];
        }

        Events::$_listeners[$eventName][] = $callback;
    }

    public static function trigger($eventName, $data = []) {
        if (isset(Events::$_listeners[$eventName])) {
            foreach (Events::$_listeners[$eventName] as $callback) {
                $callback(['eventName' => $eventName] + $data);
            }
        }
    }

    public static function listeners() {
        return Events::$_listeners;
    }

}

?>