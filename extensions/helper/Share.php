<?php

namespace li3_fieldwork\extensions\helper;

use li3_fieldwork\utils\Url;


class Share extends \lithium\template\helper\Html {

	protected static $_share_config;


    public static function config($settings) {
        Share::$_share_config = $settings;
    }


    protected $_strings = array(
        'twitter_url' => 'https://twitter.com/intent/tweet?{:qs}',
        'facebook_url' => 'https://www.facebook.com/dialog/feed?{:qs}',
        'plus_url' => 'https://plus.google.com/share?{:qs}',
        'tumblr_url' => 'http://www.tumblr.com/share/photo?{:qs}',
        'pinterest_url' => 'http://pinterest.com/pin/create/button/?{:qs}',

    	'twitter' => '<a href="{:url}" target="_blank">{:link_text}</a>',
        'facebook' => '<a href="{:url}" target="_blank">{:link_text}</a>',
        'plus' => '<a href="{:url}" target="_blank">{:link_text}</a>',
        'tumblr' => '<a href="{:url}" target="_blank">{:link_text}</a>',
        'pinterest' => '<a href="{:url}" target="_blank">{:link_text}</a>'
    );
    
    public function twitterUrl(array $params, array $options = []) {
    	$params = $params + array(
    		'url' => '',
    		'text' => '',
    		'related' => ''
    	);
    	$params['url'] = ($params['url']) ?: $this->currentPageUrl();
    	return $this->getSocialUrl('twitter', $params);
    }

    public function twitter(array $params, array $options = [], $link_text = 'Tweet') {
        return $this->getSocialLink('facebook', $this->twitterUrl($params, $options), $link_text);
    }
    
    public function facebookUrl(array $params, array $options = []) {
    	$params = $params + array(
    		'link' => '',
    		'app_id' => Share::$_share_config['fb_app_id'],
    		'picture' => '',
    		'name' => '',
    		'description' => '',
    		'redirect_uri' => ''
    	);
    	$params['link'] = ($params['link']) ?: $this->currentPageUrl();
    	$params['redirect_uri'] = ($params['redirect_uri']) ?: $params['link'];
    	return $this->getSocialUrl('facebook', $params);
    }

    public function facebook(array $params, array $options = [], $link_text = 'Share') {
        return $this->getSocialLink('facebook', $this->facebookUrl($params, $options), $link_text);
    }
    
    public function plus(array $params, array $options = [], $link_text = 'Plus') {
        $params = $params + array(
    		'url' => ''
    	);
    	$params['url'] = ($params['url']) ?: $this->currentPageUrl();
        return $this->getSocialLink('plus', $params, $link_text);
    }
    
    public function tumblr(array $params, array $options = [], $link_text = 'Tumbl') {
        $params = $params + array(
    		'source' => '',		//	Image URL
    		'caption' => '',
    		'click_thru' => ''
    	);
    	$params['click_thru'] = ($params['click_thru']) ?: $params['source'];
        return $this->getSocialLink('tumblr', $params, $link_text);
    }
    
    public function pinterest(array $params, array $options = [], $link_text = 'Pin') {
        $params = $params + array(
    		'url' => '',		//	Page URL
    		'media' => '',		//	Image/video URL
    		'description' => ''
    	);
    	$params['url'] = ($params['url']) ?: $this->currentPageUrl();
        return $this->getSocialLink('pinterest', $params, $link_text);
    }

    public function getSocialUrl($type, $query_string_params) {
        $qs = http_build_query(array_filter($query_string_params), '', '&amp;');
        return $this->_render(__METHOD__, $type . '_url', compact('qs'));
    }
    
    public function getSocialLink($type, $url, $link_text) {
        $link_text = $this->escape($link_text);
        return $this->_render(__METHOD__, $type, compact('url', 'link_text'));
    }
    
    public function currentPageUrl() {
		$url = $this->baseUrl() . $_SERVER['REQUEST_URI'];
		return $url;
	}

	public function baseUrl() {
		return Url::appUrl();
	}
}

?>
