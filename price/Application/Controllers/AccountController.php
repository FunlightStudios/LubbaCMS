<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Application\Controllers {
	    use Kitty\System\Patterns\MVC\Controllers\Controller;
	    use Kitty\System\Utils\Routing\Interfaces\IRoute;
	    use Kitty\System\Utils\Routing\Route;
		use Kitty\System\Utils\Helpers\Redirects\Redirects;

	    final class AccountController extends Controller implements IRoute {

	        public function onCall(Route $route, array $vars) {
				if($route->getData() == 'index') {
					# Index
					$this->redirectBy(Redirects::USER_NOT_LOGGED_IN, 'index');

					$tpl = $this->createView('Account.tpl', 'accountView');

					$this->displayView($tpl);
				}
	        }

	        public function getRoutes(): array {
	            return [
					new Route('/account', $this, 'index'),
					new Route('/account/index', $this, 'index')
	            ];
	        }
	    }
	}
