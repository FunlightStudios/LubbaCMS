<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Configurations {
		final class Paths {
			private $paths;

			/**
			 * @param array $paths
			 */
			public function __construct($paths) {
				if(is_object($paths)) {
					$this->paths = $paths;
				}
			}

			/**
			 * Returns the languages path
			 * @return string
			 */
			public function getLanguagesPath(): string {
				return $this->paths->languages;
			}

			/**
			 * Returns the framework path.
			 * @return string
			 */
			public function getFrameworkPath(): string {
				return $this->paths->framework;
			}

			/**
			 * Returns the site path
			 * @return string
			 */
			public function getSitePath(): string {
				return $this->paths->site;
			}

			/**
			 * Returns the libraries path
			 * @return string
			 */
			public function getLibrariesPath(): string {
				return $this->paths->libraries;
			}

			/**
			 * Returns the templates path
			 * @return string
			 */
			public function getTemplatesPath(): string {
				return $this->paths->templates;
			}

			/**
			 * Returns the models path
			 * @return string
			 */
			public function getModelsPath(): string {
				return $this->paths->models;
			}

			/**
			 * Returns the controllers path
			 * @return string
			 */
			public function getControllersPath(): string {
				return $this->paths->controllers;
			}
		}
	}
