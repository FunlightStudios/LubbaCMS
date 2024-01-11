<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Application\Controllers {
	    use Kitty\System\Patterns\MVC\Controllers\Controller;
	    use Kitty\System\Utils\Routing\Interfaces\IRoute;
	    use Kitty\System\Utils\Routing\Route;
		use Kitty\System\Utils\Helpers\Redirects\Redirects;
		use Kitty\System\Common;

		use Application\Models\Furniture\FurnitureFactory;

	    final class FurnitureController extends Controller implements IRoute {

	        public function onCall(Route $route, array $vars) {
				if($route->getData() == 'furniture') {
					# Furniture
					$tpl = $this->createView('Furniture.tpl', 'furnitureView');
					$tpl->furnitures = $this->getControllerParameters()->getModelsManager()->get(FurnitureFactory::class);

					$furniture = $tpl->furnitures->getByColumn('id', $vars['id']);
					if($furniture == null) {
						Common::redirect('index');
					}

					$tpl->furniture = $furniture;

					$this->displayView($tpl);
				}
	        }

	        public function getRoutes(): array {
	            return [
					new Route('/furniture/{int:id}', $this, 'furniture'),
					new Route('/furni/{int:id}', $this, 'furniture')
	            ];
	        }
	    }
	}
