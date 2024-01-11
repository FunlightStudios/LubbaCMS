<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Controllers {
	    use Kitty\System\Application;
	    use Kitty\System\Patterns\MVC\Controllers\Collectors\ControllerCollector;
	    use Kitty\System\Patterns\MVC\Templates\Managers\TemplateManager;
	    use Kitty\System\Patterns\MVC\Templates\View;
	    use Kitty\System\Patterns\MVC\Models\Handlers\ModelsManager;

	    final class ControllerParameters {
	        private $application;
	        private $controllerCollector;
	        private $templateManager;
	        private $view;
	        private $modelsManager;

			/**
			 * @param Application         $application
			 * @param ControllerCollector $controllerCollector
			 * @param TemplateManager     $templateManager
			 * @param View                $view
			 * @param ModelsManager       $modelsManager
			 */
	        public function __construct(Application $application, ControllerCollector $controllerCollector, TemplateManager $templateManager, View $view, ModelsManager $modelsManager) {
	            $this->application = $application;
	            $this->controllerCollector = $controllerCollector;
	            $this->templateManager = $templateManager;
	            $this->view = $view;
	            $this->modelsManager = $modelsManager;
	        }

			/**
			 * @return Application
			 */
	        public function getApplication(): Application {
	            return $this->application;
	        }

			/**
			 * @return ControllerCollector
			 */
	        public function getControllerCollector(): ControllerCollector {
	            return $this->controllerCollector;
	        }

			/**
			 * @return TemplateManager
			 */
	        public function getTemplateManager(): TemplateManager {
	            return $this->templateManager;
	        }

			/**
			 * @return View
			 */
	        public function getView():View {
	            return $this->view;
	        }

			/**
			 * @return ModelsManager
			 */
	        public function getModelsManager(): ModelsManager {
	            return $this->modelsManager;
	        }
	    }
	}
