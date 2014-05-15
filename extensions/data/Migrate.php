<?php


namespace fieldwork\extensions\data;


/*
use lithium\data\Connections;
Connections::get('default')->applyFilter('_execute', function($self, $params, $chain) {
    var_dump($params['sql']);
    return $chain->next($self, $params, $chain);
});
*/


class Migrate extends \lithium\data\Model {
	
	
	public static function migrate($return_output = false, $migrations_dir = false) {
		if ($return_output) {
			ob_start();
		}
		//	Find files in /migrate dir
		$dir = LITHIUM_LIBRARY_PATH . '/../';
		$dir .= $migrations_dir ?: 'migrations';
    	$files = scandir($dir);
    	$f = [];
    	foreach ($files as $filename) {
	    	list($key) = explode('.', $filename, 2);
	    	$f[$key] = $filename;
    	}
    	$files = $f;
    	ksort($files);
    	//	Find last migration
    	$last_migration = -1;
    	if (file_exists($dir . '/last_migration.txt')) {
	    	$last_migration = file_get_contents($dir . '/last_migration.txt');
    	}
    	echo 'Starting at ' . ($last_migration + 1) . "\n";
    	//	Step through in sequence and execute SQL
    	$lm = false;
    	foreach ($files as $filename) {
	    	if (pathinfo($dir . '/' . $filename, PATHINFO_EXTENSION) === 'sql') {
		    	if ($sql = file_get_contents($dir . '/' . $filename)) {
		    		list($number) = explode('.', $filename, 2);
		    		if ($number > $last_migration) {
				    	$sql = explode(';', $sql);
				    	$query_count = 0;
			    		foreach ($sql as $query) {
			    			if (trim($query)) {
			    				Migrate::connection()->read($query);
			    				$query_count ++;
			    			}
			    		}
			    		if ($query_count) {
			    			echo 'Executing migration ' . $filename . '... ';
				    		echo "success – " . $query_count . " queries executed\n";
							$lm = $number;
						}
						else {
							echo 'Executing migration ' . $filename . '... ';
				    		echo "No queries in migration file.\n";
						}
				    }
		    	}
	    	}
    	}
    	//	Update last migration file
    	if ($lm) {
    		file_put_contents($dir . '/last_migration.txt', $lm);
			echo 'Now at ' . $lm . "\n";
		}
		else {
			echo "Already up to date.\n";
		}
		if ($return_output) {
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
		return true;
    }
    
	

}

?>