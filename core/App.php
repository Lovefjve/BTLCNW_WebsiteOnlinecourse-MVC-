<?php
// Minimal App helper with guard to avoid redeclaration
if (!class_exists('App')) {
	class App {
		/**
		 * Return base URL if defined by the app bootstrap, otherwise try to compute it.
		 */
		public static function baseUrl() {
			if (defined('BASE_URL')) return constant('BASE_URL');
			$script = $_SERVER['SCRIPT_NAME'] ?? '';
			$base = rtrim(str_replace('\\', '/', dirname($script)), '/');
			return $base === '/' ? '' : $base;
		}
	}
}
