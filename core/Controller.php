<?php
// Minimal base Controller with guards to avoid redeclaration
if (!class_exists('Controller')) {
	class Controller {
		/**
		 * Load and instantiate a model by name (e.g., 'User')
		 * Returns instance or null.
		 */
		protected function loadModel($name) {
			$file = __DIR__ . '/../models/' . $name . '.php';
			if (file_exists($file)) {
				require_once $file;
				if (class_exists($name)) return new $name();
			}
			return null;
		}

		/**
		 * Render a view under views/ with extracted data
		 */
		protected function render($viewPath, $data = []) {
			$file = __DIR__ . '/../views/' . ltrim($viewPath, '/');
			if (!file_exists($file)) return false;
			extract(is_array($data) ? $data : []);
			require $file;
			return true;
		}

		/**
		 * Simple redirect helper
		 */
		protected function redirect($url) {
			header('Location: ' . $url);
			exit;
		}
	}
}
