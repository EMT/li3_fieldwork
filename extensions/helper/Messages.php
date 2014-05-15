<?php

/**
 * View helper for fieldwork/Messages
 */

namespace fieldwork\extensions\helper;
use fieldwork\messages\Messages as FieldworkMessages;


class Messages extends \lithium\template\Helper {


	protected $_strings = array(
		'fw_msgs_error' => '<li class="message error">{:msg}</li>',
		'fw_msgs_success' => '<li class="message success">{:msg}</li>',
		'fw_msgs_neutral' => '<li class="message neutral">{:msg}</li>',
		
		'fw_msgs_error_wrapper' => '<ul class="message-group error">{:msgs}</ul>',
		'fw_msgs_success_wrapper' => '<ul class="message-group success">{:msgs}</ul>',
		'fw_msgs_neutral_wrapper' => '<ul class="message-group neutral">{:msgs}</ul>',
		
		'fw_msgs_wrapper' => '<div class="messages">{:groups}</div>'
	);

	
	public function render(array $options = array()) {
		$messages = FieldworkMessages::get();
		FieldworkMessages::clear();

		//	Group messages by type
		$msgs = array();
		foreach ($messages as $k => $m) {
			if (!isset($msgs[$m[0]])) {
				$msgs[$m[0]] = array();
			}
			$msgs[$m[0]][$k] = $m;
		}

		// Render
		$output = '';
		list($scope, $options) = $this->_options(array(), $options);
		foreach (array('error', 'success', 'warning', 'neutral') as $type) {
			if (isset($msgs[$type]) && count($msgs[$type])) {
				$ms = '';
				foreach ($msgs[$type] as $msg) {
					$ms .= $this->_renderMessage($msg, $options);
				}
				$output .= $this->_render(__METHOD__, 'fw_msgs_' . $type . '_wrapper', 
								array('msgs' => $ms), $scope);
			}
		}
		return ($output) ? $this->_render(__METHOD__, 'fw_msgs_wrapper', array('groups' => $output), $scope) : '';
	}
	
	
	private function _renderMessage(array $message, array $options = array()) {
		list($scope, $options) = $this->_options(array(), $options);
		$arguments = ['type' => $message[0], 'msg' => $message[1]];
	
		return $this->_render(__METHOD__, 'fw_msgs_' . $message[0], $arguments, $scope);
	}
	

}


?>