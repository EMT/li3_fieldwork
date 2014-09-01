<?php


//	Set up Access rules

use li3_fieldwork\access\Access;
use li3_fieldwork\email\Email;
use li3_fieldwork\messages\Messages;
use lithium\action\Dispatcher;

Access::setBehaviours(array(
	'default' => array(
		'authenticated' => function() {throw new \Exception('You don’t have access to this page.', 403); },
		'unauthenticated' => function() {
			header('Location: /login?auth_and_go_to=' . urlencode($_SERVER["REQUEST_URI"]), true, 302);
			exit();
		}
	)
));

Access::setRules([
	
]);


Email::config([
	'mandrill_api_key' => '',
	'from_name' => '',
	'from_email' => ''
]);

Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
    $request = $params['request'];
    $controller = $chain->next($self, $params, $chain);

    if (is_callable([$controller, 'applyFilter'])) {
	    $controller->applyFilter('redirect', function($self, $params, $chain) {   
			// Save unrendered mesages
			Messages::save();

			$response = $chain->next($self, $params, $chain);
		    return $response;
		});
	}
    
    return $controller;
});


?>