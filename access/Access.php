<?php

namespace fieldwork\access;


class Access {

	
	private static $_rules = array();
	private static $_behaviours = array();
	private static $_opts = array(
		'role_key' => 'role'
	);

	
	public static function setOpts(array $opts) {
		self::$_opts = $opts + self::$_opts;
	}
	

	public static function setRules(array $rules) {
		self::$_rules = $rules + self::$_rules;
	}
	
	
	public static function getRules($role = false) {
		if (!$role) {
			return self::$_rules;
		}
		if (isset(static::$_rules[$role])) {
			return self::$_rules[$role];
		}
		return false;
	}
	
	
	public static function setBehaviours(array $behaviours) {
		self::$_behaviours = $behaviours + self::$_behaviours;
	}
	
	
	public static function getBehaviours($behaviour = false) {
		if (!$behaviour) {
			return self::$_behaviours;
		}
		if (isset(static::$_behaviours[$behaviour])) {
			return self::$_behaviours[$behaviour];
		}
		return false;
	}
	
	
   /**
	* Check an authenticated user against a set of rules.
	*
	* @param Object $user An `Entity` representing an authenticated user
	* @param String/Array $roles A string or array of strings identifying one or more of the 
	*		rules set using `Acl::setRules()`; or an array where keys are strings identifying
	*		rules set using `Acl::setRules()` and values are data objects to check against.
	* @param String/Array/Function $behaviour Defines what happens when a check passes and/or fails. One of:
	*
	* - A callback function to execute if the check fails
	* - An array where keys are `logged_in` and `logged_out` and values are callbacks for each state
	* - A string refering to a behaviour set using the `setBehaviours` method
	* - A URL to redirect to on failure
	* - Boolean `false` will return `false` for a failed check
	* 
	* Default behaviour is to throw an `Exception`
	*
	* Examples:
	*
	* Check that `$user` is an administrator, according to the `admin` rule.
	*
	* 	Acl::check($user, 'admin');
	*
	* Check that `$user` is an editor of `$article`, according to the `editor` rule.
	*
	* 	Acl::check($user, array('editor' => $article));
	*
	* Check that `$user` is either an editor or a viewer of `$article`, according to the either rule.
	*
	* 	Acl::check($user, array('editor' => $article, 'viewer' => $article));
	*
	* Check that `$user` is an owner of `$article` or an admin for `$section`, according 
	* to either rule.
	*
	* 	Acl::check($user, array('owner' => $article, 'admin' => $section));
	*
	* Check that the `$user` is an editor of `$article`. If the check fails and the user is logged out,
	* redirect to the /login page. If the check fails and the user is logged in, throw an exception.
	* Note that no authentication takes place, $user is assumed to be looged in if not falsy.
	*
	* 	Acl::check($user, 'editor', $article, array(
	*		'unauthenticated' => '/login', 
	*		'authenticated' => function(
	*			throw new \Exception('You do not have permission to access this resource.', 403);
	*		)
	*	));
	*/ 

	public static function check($user, $roles = false, $behaviour = 'default') {
		if (isset(self::$_opts['authenticated'])) {
			$authenticated = self::$_opts['authenticated']($user);
		}
		else {
			$authenticated = self::_defaultAuthenticatedCheck($user);
		}
		if ($authenticated) {
			if (!$roles) {
				return true;
			}
			if (!is_array($roles)) {
				$roles = array($roles);
			}
			foreach ($roles as $role => $rsrc) {
				if (is_int($role)) {
					$role = $rsrc;
				}
				if ($rule = static::getRules($role)) {
					foreach ($rule as $rl => $conds) {
						if ($conds === true && $user->{self::$_opts['role_key']} === $rl) {
							return true;
						}
						if (is_array($conds)) {
							$passed = true;
							foreach ($conds as $user_field => $match) {
								if (strpos($match, ':') === 0 && $rsrc) {
									$fld = substr($match, 1);
									$match = $rsrc->$fld;
								}
								if (!is_int($rl) && $user->{self::$_opts['role_key']} !== $rl) {
									$passed = false;
									break;
								}
								if (!$rsrc || $user->$user_field !== $match) {
									$passed = false;
									break;
								}
							}
							if ($passed) {
								return true;
							}
						}
						if (is_callable($conds)) {
							if ($conds($user, $rsrc)) {
								return true;
							}
						}
					}
				}
				else {
					throw new \Exception('Rules not found for ' . $role, 500);
				}
			}
		}
		if ($behaviour) {
			$status = ($authenticated) ? 'authenticated' : 'unauthenticated';
			$b = false;
			if (is_array($behaviour) && isset($behaviour[$status])) {
				$b = $behaviour[$status];
			}
			else if (is_string($behaviour) && $b = static::getBehaviours($behaviour)) {
				if (is_array($b) && isset($b[$status])) {
					$b = $b[$status];
				}
			}
			else if (is_string($behaviour) || is_callable($behaviour)) {
				$b = $behaviour;
			}
			if (is_callable($b)) { 
				$b();
			}
			else if (is_string($b)) {
				header('Location: ' . $b);
				exit();
			}
			else {
				throw new \Exception(
					'Access denied. Behaviour `' . $behaviour . '` not found.', 500);
			}
		}
		return false;
	}
	
	
   /**
    * Default method for determining if the $user object represents an authenticated user
    * Override this calling:
    *     setOpts(array('authenticated' => function() { ... do check here ... }));
    */
    
	private static function _defaultAuthenticatedCheck($user) {
		return ($user);
	}

}

?>