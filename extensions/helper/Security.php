<?php

namespace fieldwork\extensions\helper;

class Security extends \lithium\template\helper\Security {

	private static $_token;

	/**
	 * Generates a request key used to protect your forms against CSRF attacks. See the
	 * `RequestToken` class for examples and proper usage.
	 *
	 * @see lithium\security\validation\RequestToken
	 * @param array $options Options used as HTML when generating the field.
	 * @return string Returns a hidden `<input />` field containing a request-specific CSRF token
	 *         key.
	 */
	public function requestTokenOnly() {
		$requestToken = $this->_classes['requestToken'];

		$flags = array_intersect_key($this->_config, array('sessionKey' => '', 'salt' => ''));
		$value = $requestToken::key($flags);

		return $value;
	}

	public function requestTokenOnce() {
		if (!static::$_token) {
			$requestToken = $this->_classes['requestToken'];
			$flags = array_intersect_key($this->_config, array('sessionKey' => '', 'salt' => ''));
			$value = $requestToken::key($flags);
			static::$_token = $value;
		}

		return static::$_token;
	}
}

?>