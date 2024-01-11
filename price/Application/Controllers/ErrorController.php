<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Application\Controllers {
	    use Kitty\System\Patterns\MVC\Controllers\Controller;
	    use Kitty\System\Utils\Routing\Interfaces\IRoute;
	    use Kitty\System\Utils\Routing\Route;

	    final class ErrorController extends Controller implements IRoute {

	        public function onCall(Route $route, array $vars) {
				if($route->getData() == 'error') {
					$tpl = $this->createView('Error.tpl', 'errorView');

					$this->displayView($tpl);
				}
	        }

	        public function getRoutes(): array {
	            return [
	                new Route('/error', $this, 'error')
	            ];
	        }
	    }
	}
