<?php

namespace fieldwork\extensions\helper;

class DateTime extends \lithium\template\Helper {

	public function ago($unix) {
		$dt = new \DateTime(date('Y-m-d H:i:s', $unix));
		$now = new \DateTime(null);
		$diff = $dt->diff($now);
		if ($m = $diff->format('%m')) {
			return $m . ' month' . (($m <> 1) ? 's' : '');
		}
		else if ($d = $diff->format('%d')) {
			return $d . ' day' . (($d <> 1) ? 's' : '');
		}
		else if ($h = $diff->format('%h')) {
			return $h . ' hour' . (($h <> 1) ? 's' : '');
		}
		else if ($i = $diff->format('%i')) {
			return $i . ' minute' . (($i <> 1) ? 's' : '');
		}
		$s = $diff->format('%s');
		return $s . ' second' . (($s <> 1) ? 's' : '');
	}

}

?>