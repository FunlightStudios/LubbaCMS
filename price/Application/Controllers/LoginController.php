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

		use Application\Models\User\UserFactory;
		use Application\Models\Settings\SettingsFactory;

	    final class LoginController extends Controller implements IRoute {

	        public function onCall(Route $route, array $vars) {
				$settings = $this->getControllerParameters()->getModelsManager()->get(SettingsFactory::class);

				if($route->getData() == 'login') {
					# Login post
					$this->redirectBy(Redirects::USER_LOGGED_IN, 'account');
					$tpl = $this->createView('Login.tpl', 'loginView');

					if(isset($_POST['username'], $_POST['password'])) {
						$userFactory = $this->getControllerParameters()->getModelsManager()->get(UserFactory::class);
						$user = $userFactory->getByColumn('username', $_POST['username']);

						if($user != null && $user->getRow()->password == $userFactory->getHashedString($_POST['password'])) {
							$this->setSession('USERID', $user->getRow()->id);
							$this->setSession('PASSWORD', $user->getRow()->password);
							Common::redirect('account');
						} else {
							$tpl->text = $this->getLanguage()->json()['Login']['Error'];
						}
					}

					$this->displayView($tpl);
				} else if($route->getData() == 'loginJsonResponse') {
					# Login ajax
					$json = [
						'Success' => false,
						'Message' => $this->getLanguage()->json()['Login']['Error']
					];

					if(isset($_POST['username'], $_POST['password'])) {
						$userFactory = $this->getControllerParameters()->getModelsManager()->get(UserFactory::class);
						$user = $userFactory->getByColumn('username', $_POST['username']);

						if($user != null && $user->getRow()->password == $userFactory->getHashedString($_POST['password'])) {
							$this->setSession('USERID', $user->getRow()->id);
							$this->setSession('PASSWORD', $user->getRow()->password);

							$json['Success'] = true;
							$json['Redirect'] = $settings->getSitePath() . '/account';
							unset($json['Message']);
						}
					}

					echo json_encode($json, JSON_PRETTY_PRINT);
				}
			}

	        public function getRoutes(): array {
	            return [
		            new Route('/login/post', $this, 'loginJsonResponse'),
		            new Route('/login', $this, 'login')
	            ];
	        }
	    }
	}
