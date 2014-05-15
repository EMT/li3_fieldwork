<?php

namespace fieldwork\extensions\command;

use fieldwork\extensions\data\Migrate as MigrateModel;
use lithium\data\Connections;


class Migrate extends \lithium\console\Command {

    public function run() {
    	$migrations_dir = !empty($this->lib) ? 
    		'libraries/' . $this->lib . '/migrations' : false;
		MigrateModel::migrate(false, $migrations_dir);
    }
}

?>
