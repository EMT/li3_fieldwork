<?php

/**
 * Pagination in Lithium views
 */

namespace li3_fieldwork\extensions\helper;

use li3_fieldwork\paginate\Paginate as P;


class Paginate extends \lithium\template\helper\Html {

	public function render(array $options = []) {
		$options = $options + [
			'render_disabled' => false,
			'disabled_class' => 'disabled'
		];

		extract(P::getPagination());
		$controller = $this->_context->_config['request']->params['controller'];
		$action = $this->_context->_config['request']->params['action'];
		$html = '<ul class="pagination">';
		if ($page > 1) {
			$html .= '<li class="prev">' . $this->link('Previous', [$controller . '::' . $action, 'page' => $page - 1, '?' => $qs_data]) . '</li>';
		}
		else if (!empty($options['render_disabled'])) {
			$html .= '<li class="prev ' . $options['disabled_class'] . '"><span>Previous</span></li>';
		}
		if ($next) {
			$html .= '<li class="next">' . $this->link('Next', [$controller . '::' . $action, 'page' => $page + 1, '?' => $qs_data]) . '</li>';
		}
		else if (!empty($options['render_disabled'])) {
			$html .= '<li class="next ' . $options['disabled_class'] . '"><span>Next</span></li>';
		}
		$html .= '</ul>';
		return $html;
	}

	public function __get($name) {
		$properties = P::getPagination();
		return (isset($properties[$name])) ? $properties[$name] : null;
	}

}


?>