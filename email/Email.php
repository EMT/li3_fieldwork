<?php


/**
 * Handles all email for an app.
 * Currently only implements Mandrill
 */

namespace li3_fieldwork\email;


class Email {


	protected $mandrill;
	protected $from_email;
	protected $from_name;
	protected $template_dir;
	protected $footer_template = 'footer';

	protected static $_config;


	public static function config($settings) {
		Email::$_config = $settings;
	}
	
	
	public function __construct(array $options = []) {
		$options = $options + Email::$_config;
		require_once 'mandrill/Mandrill.php'; //Not required with Composer
		$this->mandrill = new \Mandrill($options['mandrill_api_key']);
		$this->from_email = $options['from_email'];
		$this->from_name = $options['from_name'];
		$this->reply_to = $options['reply_to'];

		$this->merge = (empty($options['merge'])) ? [] : $options['merge'];
		$this->footer_template = (empty($options['footer_template'])) ? null : $options['footer_template'];
		$this->template_dir = (empty($options['template_dir'])) ? __DIR__ . '/templates/' : $options['template_dir'];
	}
	
	
	public function send($message) {
		try {
		    $async = false;
		    $ip_pool = 'Main Pool';
/* 		    $send_at = date('Y-m-d H:i:s', time() - (3600*24*1000)); */
			$send_at = null;
		    return $this->mandrill->messages->send($message, $async, $ip_pool, $send_at);
		} catch(Mandrill_Error $e) {
		    echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		    throw $e;
		}
	}
	
	
	public function sendTemplate($message, $template, $data) {
		$data = $data + $this->merge;

		if (file_exists($this->template_dir . $template . '.txt')) {
			$content = file_get_contents($this->template_dir . $template . '.txt');
			if ($content) {
				if (file_exists($this->template_dir . $this->footer_template . '.txt')) {
					$content .= file_get_contents($this->template_dir . $this->footer_template . '.txt');
				}
				$text = $this->_parseTemplate($content, $data);
				$message['text'] = $text['body'];
			}
			else {
				$message['text'] = null;
			}
		}
		if (file_exists($this->template_dir . $template . '.html')) {
			$content = file_get_contents($this->template_dir . $template . '.html');
			if ($content) {
				if (file_exists($this->template_dir . $this->footer_template . '.html')) {
					$content .= file_get_contents($this->template_dir . $this->footer_template . '.html');
				}
				$html = $this->_parseTemplate($content, $data, true);
				$message['html'] = $html['body'];
			}
			else {
				$message['html'] = null;
			}
		}
		$message['subject'] = ($text) ? $text['subject'] : $html['subject'];
		$message['tags'] = [$template];
		$message['from_email'] = $this->from_email;
		$message['from_name'] = $this->from_name;
		$message['headers'] = [
			'Reply-To' => $this->reply_to
		];
/* 	var_dump($message); exit(); */
		return $this->send($message);
	}
	
	
	private static function _parseTemplate($content, $data, $newlines = false) {
		$search = array();
		$replace = array();
		foreach ($data as $key => $val) {
			if (!is_array($val) && !is_object($val)) {
				$search[] = '{{' . $key .'}}';
				$replace[] = ($newlines) ? nl2br($val) : $val;
			}
		}
		$content = trim($content);
		list($subject, $body) = explode("\n", $content, 2);
		$subject = str_replace($search, $replace, trim($subject));
		$body = str_replace($search, $replace, trim($body));
		return compact('subject', 'body');
	}

}


?>