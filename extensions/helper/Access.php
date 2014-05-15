<?php

/**
 * Wrapper for using Access in Lithium views
 */

namespace li3_fieldwork\extensions\helper;
use li3_fieldwork\access\Access as FieldworkAccess;


class Access extends \lithium\template\Helper {

	public function check($user, $roles = false, $throw_exception = false) {
		return FieldworkAccess::check($user, $roles, $throw_exception);
	}

}


?>