<?php

namespace li3_fieldwork\paginate;


class Paginate {
	
	
	protected static $_pagination_data = [];
	
	
	public static function setPagination($collection, array $pagination, array $options = []) {
		$model = (!empty($collection)) ? $collection->model() : 'model';
		static::$_pagination_data[$model] = static::getPaginationData($collection, $pagination, $options);
	}
	
	
	public static function getPagination($collection = false) {
		$model = (!empty($collection)) ? $collection->model() : 'model';
		if ($collection && isset(static::$_pagination_data[$model])) {
			return static::$_pagination_data[$model];
		}
		$data = array_values(static::$_pagination_data);
		$p = array_shift($data);
		return ($p) ?: ['next' => false];
	}
	
	
	public static function getPaginationData($collection, array $pagination, array $options = []) {
    	extract($pagination);
    	$next = false;
    	if (!empty($collection)) {
	    	$model = $collection->model();
		    $next = (count($collection) >= $limit) ? $model::all(
		    		$options + ['page' => ($page * $limit) + 1, 'limit' => 1]) : false;
	    	$next = ($next && count($next)) ? true : false;
	    }
    	return [
    		'next' => $next,
    	] + $pagination;
    }

}

?>