<?php


namespace li3_fieldwork\extensions\data;


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
		    	if ($sql = trim(file_get_contents($dir . '/' . $filename))) {
		    		list($number) = explode('.', $filename, 2);
		    		if ((int)$number > (int)$last_migration) {
				    	$query_count = 0;
				    	$quote_types = ["'", '"'];
				    	$delimiter = ';';
						$quoted = [];
						$queries = [];
						$query_start = 0;
						$char = null;

						if (strpos($sql, $delimiter) !== false) {
							for ($i = 0, $len = strlen($sql); $i < $len; $i ++) {
								$previous_char = $char;
								$char = substr($sql, $i, 1);
								if (in_array($char, $quote_types) && $previous_char !== '\\') {
									if (empty($quoted[$char])) {
										$quoted[$char] = true;
									}
									else {
										unset($quoted[$char]);
									}
								}

								if ($char === $delimiter && !count($quoted)) {
									$query = substr($sql, $query_start, $i - $query_start + 1);
									$query_start = $i + 1;

									if (trim($query)) {
					    				Migrate::connection()->read($query);
					    				$query_count ++;
					    			}
								}
							}
						}
						else if ($sql) {
		    				Migrate::connection()->read($sql);
		    				$query_count ++;
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
    
	
   /**
    * Takes a query string and returns an array of queries split by the ; delimiter
    */
	private static function _splitQueries($string, $delimiter = ';') {
		$quote_types = ["'", '"'];
		$quoted = [];
		$queries = [];
		$query_start = 0;

		for ($i = 0, $len = strlen($string); $i < $len; $i ++) {
			$char = substr($string, $i, 1);

			if (in_array($char, $quote_types)) {
				if (empty($quoted[$char])) {
					$quoted[$char] = true;
				}
				else {
					unset($quoted[$char]);
				}
			}

			if ($char === $delimiter && empty($quoted)) {
				$queries[] = substr($string, $query_start, $i - 1);
				$query_start = $i + 1;
			}
		}

		return $queries;
	}

}

?>