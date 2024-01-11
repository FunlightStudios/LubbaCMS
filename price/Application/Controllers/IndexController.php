<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Application\Controllers {
	    use Kitty\System\Patterns\MVC\Controllers\Controller;
	    use Kitty\System\Utils\Routing\Interfaces\IRoute;
	    use Kitty\System\Utils\Routing\Route;

		use Application\Models\Furniture\FurnitureFactory;

	    final class IndexController extends Controller implements IRoute {

	        public function onCall(Route $route, array $vars) {
				if($route->getData() == 'index') {
					# Index
					$tpl = $this->createView('Index.tpl', 'indexView');
					$tpl->furnitures = $this->getControllerParameters()->getModelsManager()->get(FurnitureFactory::class);

					$this->displayView($tpl);
				}
	        }

	        public function getRoutes(): array {
	            return [
	                new Route('/', $this, 'index'),
		            new Route('/index', $this, 'index')
	            ];
	        }
	    }
	}
