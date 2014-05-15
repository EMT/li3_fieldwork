<?php


namespace li3_fieldwork\controllers;

use li3_fieldwork\extensions\data\Migrate;


class WebConsoleController extends \lithium\action\Controller {


	public function migrate() {
		$migrations_dir = $this->request->lib ? 
    		'libraries/' . $this->request->lib . '/migrations' : false;
		echo nl2br(Migrate::migrate(true, $migrations_dir));
		exit();
	}


}


?>