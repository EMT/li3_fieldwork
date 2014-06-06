<?php


namespace li3_fieldwork\extensions\data;

use li3_fieldwork\utils\Geocoder;

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
    

    public static function asArray($options) {
        if ($data = self::all($options)) {
            foreach ($data as $entity) {
                $result[] = $entity->{$options['fields']};
            }
            return $result;
        }
    }
    

   /**
    * Use the postcode property to find the lat and long coords
    */
    
    public function geoLocate($entity, $key = 'postcode', $debug = false) {
        if (!$entity->$key) {
            throw new \Exception('No key `' . $key . '` for this entity.', 500);
        }

        $address = $entity->$key;
        $geocoder = new Geocoder($address, 'uk');
        $geometry = $geocoder->getGeometry();

        if ($geometry) { 
            $entity->latitude = $geometry->location->lat;
            $entity->longitude = $geometry->location->lng;
            $entity->geo_accuracy = $geometry->location_type;
            return $entity;
        }
        
        if ($debug) {
            return $geocoder->getRaw();
        }

        return false;
    }
    

}






?>