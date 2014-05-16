<?php


namespace li3_fieldwork\extensions\data;


/*
use lithium\data\Connections;
Connections::get('default')->applyFilter('_execute', function($self, $params, $chain) {
    var_dump($params['sql']);
    return $chain->next($self, $params, $chain);
});
*/


class Model extends \lithium\data\Model {



    public function save($entity, $data = null, array $options = array()) {
    
        if ($data) {
            $entity->set($data);
        }
    
        //  Set modified field keys
        $modified = array_keys($entity->modified(), true, true);
        $exclude = array_diff($modified, array_keys($entity->schema()->fields()));
        $entity->_updated_fields = array_diff($modified, $exclude);
    
        //  Set created and updated timestamps
        $entity->updated = time();
        if (!$entity->exists() && empty($entity->created)) {
            $entity->created = $entity->updated;    
        }
        
        return parent::save($entity, null, $options);
    }
    
    
    // public static function exists($conditions) {
    //     return (self::first(array('conditions' => $conditions, 'fields' => array('id'))));
    // }
    
    
    public function humanBoolean($entity, $field) {
        return ($entity->$field) ? 'Yes' : 'No';
    }
    
    
    public function humanDate($entity, $field, $format = 'j M Y') {
        if (time() - $entity->$field < 60*60*12*365) {
            $format = trim(str_replace('Y', '', $format));
        }
        return date($format, $entity->$field);
    }
    

    // public static function asArray($options) {
    //     if ($data = self::all($options)) {
    //         foreach ($data as $entity) {
    //             $result[] = $entity->{$options['fields']};
    //         }
    //         return $result;
    //     }
    // }
    

}






?>