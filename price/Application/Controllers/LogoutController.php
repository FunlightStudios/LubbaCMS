<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Application\Controllers {
	    use Kitty\System\Patterns\MVC\Controllers\Controller;
	    use Kitty\System\Utils\Routing\Interfaces\IRoute;
	    use Kitty\System\Utils\Routing\Route;
		use Kitty\System\Common;

	    final class LogoutController extends Controller implements IRoute {

	        public function onCall(Route $route, array $vars) {
				/*if($route->getData() == 'logout') {
					session_destroy();
					Common::redirect('index');
				}*/

				session_destroy();
				Common::redirect('index');
	        }

	        public function getRoutes(): array {
	            return [
					new Route('/logout', $this, 'logout'),
					new Route('/account/logout', $this, 'logout')
	            ];
	        }
	    }
	}
