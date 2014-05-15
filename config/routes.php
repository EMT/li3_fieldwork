<?php

use lithium\net\http\Router;
use lithium\core\Environment;


/**
 * Because MAMP’s setup doesn’t like li3 console commands
 */
if (Environment::is('development')) { 
	Router::connect('/console/migrate/{:lib}', ['controller' => 'WebConsole', 'action' => 'migrate']);
} 

?>