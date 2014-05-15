<?php



/**
*	Geocoding class for Google Geocoding API
*/

namespace fieldwork\utils;



class Geocoder {
	
	private $_base = 'http://maps.googleapis.com/maps/api/geocode/json';
	private $_address;
	private $_sensor;
	private $_region;
	private $_result;
	
	
	function __construct($address, $region = false, $sensor = false) {
		$this->_address = $address;
		$this->_sensor = $sensor;
		$this->_region = $region;
		$url = $this->_base . '?address=' . urlencode($this->_address);
		if ($this->_region) {$url .= '&region=' . $this->_region; }
		$url .= '&sensor=';
		$url .= ($this->_sensor) ? 'true' : 'false';
		$this->_result = json_decode(@file_get_contents($url));
	}
	
	function getGeometry() {
		if ($this->_result && $this->_result->status == 'OK') {
			return $this->_result->results[0]->geometry;
		}
		return false;
	}
	
	function getLatLng() {
		if ($this->_result->status == 'OK') {
			return $this->_result->results[0]->geometry->location;
		}
		return false;
	}
	
	function dump() {
		var_dump($this->_result);
	}
	

}



?>