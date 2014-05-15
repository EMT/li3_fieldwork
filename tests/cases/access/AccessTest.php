<?php

/**
 * Access control library
*/

namespace fieldwork\tests\cases\access;

use \fieldwork\access\Access;


class AccessTest extends \lithium\test\Unit {

	public function setUp() {}
	
	public function tearDown() {}
	
	public function testSimpleRoleRule() {
		$user = new \stdClass;
		//	If $user->role === 'admin then allow
		Access::setRules(array(
			'is_admin' => array(
				'admin' => true
			),
			'another_rule' => array(
				'other_role' => true
			)
		));
		
		$user->role = 'admin';
		$this->assertTrue(Access::check($user, 'is_admin', false));
		$user->role = 'other_role';
		$this->assertTrue(Access::check($user, array('is_admin', 'another_rule'), false));
		$user->role = 'yet_another_role';
		$this->assertFalse(Access::check($user, array('is_admin', 'another_rule'), false));
	}
	
	public function testSimpleFieldRule() {
		$user = new \stdClass;
		$resource = new \stdClass;
		$resource->user_id = 99;
		//	If $user ID is equal to $resource ID then allow
		Access::setRules(array(
			'is_owner' => array(
				array('id' => ':user_id')
			)
		));
		
		$user->id = 99;
		$this->assertTrue(Access::check($user, array('is_owner' => $resource), false));
		$user->id = 5;
		$this->assertFalse(Access::check($user, array('is_owner' => $resource), false));
		$user->id = 0;
		$this->assertFalse(Access::check($user, array('is_owner' => $resource), false));
		$user->id = false;
		$this->assertFalse(Access::check($user, array('is_owner' => $resource), false));
	}
	
	public function testRoleFieldRule() {
		$user = new \stdClass;
		$resource = new \stdClass;
		$resource->user_id = 99;
		//	If $user->role === 'admin' AND $user ID is equal to $resource ID then allow
		Access::setRules(array(
			'is_admin_and_owner' => array(
				'admin' => array('id' => ':user_id')
			)
		));
		
		$user->role = 'admin';
		$user->id = 99;
		$this->assertTrue(Access::check($user, array('is_admin_and_owner' => $resource), false));
		$user->id = 5;
		$this->assertFalse(Access::check($user, array('is_admin_and_owner' => $resource), false));
		$user->id = 0;
		$this->assertFalse(Access::check($user, array('is_admin_and_owner' => $resource), false));
		$user->id = false;
		$this->assertFalse(Access::check($user, array('is_admin_and_owner' => $resource), false));
		$user->role = 'other_role';
		$user->id = 99;
		$this->assertFalse(Access::check($user, array('is_admin_and_owner' => $resource), false));
	}
	
	public function testMultipleRoleFieldRule() {
		$user = new \stdClass;
		$resource = new \stdClass;
		$resource->user_id = 99;
		$resource->creator_id = 5;
		//	If $user ID is equal to $resource ID OR role is 'editor' 
		//	AND $user ID is equal to $resource creator_id then allow
		Access::setRules(array(
			'is_owner_or_editor' => array(
				array('id' => ':user_id'),
				'editor' => array('id' => ':creator_id')
			)
		));
		
		$user->role = 'admin';
		$user->id = 99;
		$this->assertTrue(Access::check($user, array('is_owner_or_editor' => $resource), false));
		$user->id = 5;
		$this->assertFalse(Access::check($user, array('is_owner_or_editor' => $resource), false));
		$user->id = 0;
		$this->assertFalse(Access::check($user, array('is_owner_or_editor' => $resource), false));
		$user->id = false;
		$this->assertFalse(Access::check($user, array('is_owner_or_editor' => $resource), false));
		$user->role = 'other_role';
		$user->id = 99;
		$this->assertTrue(Access::check($user, array('is_owner_or_editor' => $resource), false));
		
		$user->role = 'editor';
		$user->id = 99;
		$this->assertTrue(Access::check($user, array('is_owner_or_editor' => $resource), false));
		$user->id = 5;
		$this->assertTrue(Access::check($user, array('is_owner_or_editor' => $resource), false));
		$user->id = 0;
		$this->assertFalse(Access::check($user, array('is_owner_or_editor' => $resource), false));
		$user->id = false;
		$this->assertFalse(Access::check($user, array('is_owner_or_editor' => $resource), false));
		$user->role = 'other_role';
		$user->id = 5;
		$this->assertFalse(Access::check($user, array('is_owner_or_editor' => $resource), false));
	}
	
	public function testRoleBasedFunctionRule() {
		$user = new \stdClass;
		$resource = new \stdClass;
		//	$user->role is 'admin' and the function returns true
		Access::setRules(array(
			'is_admin_and_something' => array(
				'admin' => function($user, $resource) {
					if (in_array($user->id, array(22, 14))) {
						return true;
					}
					if (is_object($resource) && isset($resource->id) 
					&& in_array($resource->id, array(1, 2))) {
						return true;
					}
					return false;
				}
			)
		));
		
		$user->role = 'admin';
		$user->id = 22;
		$this->assertTrue(Access::check($user, 'is_admin_and_something', false));
		$user->id = 14;
		$this->assertTrue(Access::check($user, 'is_admin_and_something', false));
		$user->id = 15;
		$this->assertFalse(Access::check($user, array('is_admin_and_something' => $resource), false));
		$resource->id = 1;
		$this->assertTrue(Access::check($user, array('is_admin_and_something' => $resource), false));
		$resource->id = 2;
		$this->assertTrue(Access::check($user, array('is_admin_and_something' => $resource), false));
		$resource->id = 3;
		$this->assertFalse(Access::check($user, array('is_admin_and_something' => $resource), false));
		$user->role = 'other_role';
		$this->assertFalse(Access::check($user, array('is_admin_and_something' => $resource), false));
	}
	
	
	
}

?>