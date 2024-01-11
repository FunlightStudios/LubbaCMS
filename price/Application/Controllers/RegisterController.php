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

	    final class RegisterController extends Controller implements IRoute {

	        public function onCall(Route $route, array $vars) {
				$settings = $this->getControllerParameters()->getModelsManager()->get(SettingsFactory::class);

				if($route->getData() == 'registerJsonResponse') {
					# Register ajax
					$json = [
						'Success' => false,
						'Message' => $this->getLanguage()->json()['Register']['Error1']
					];

					if(isset($_POST['username'], $_POST['password'], $_POST['password_confirm'])) {
						if(strlen($_POST['username']) && strlen($_POST['password']) && strlen($_POST['password_confirm'])) {
							$userFactory = $this->getControllerParameters()->getModelsManager()->get(UserFactory::class);
							$user = $userFactory->getByColumn('username', $_POST['username']);

							if($user == null) {
								if(strlen($_POST['username']) >= 3) {
									if(strlen($_POST['username']) <= 16) {
										if(Common::isValidUsername($_POST['username'])) {
											if(strlen($_POST['password']) >= 6) {
												if($_POST['password'] === $_POST['password_confirm']) {
													$hashedPassword = $userFactory->getHashedString($_POST['password_confirm']);

													$registeredUser = $userFactory->_create([
														'username' => $_POST['username'],
														'password' => $hashedPassword
													]);

													$this->setSession('USERID', (string) $registeredUser->getRow()->id);
													$this->setSession('PASSWORD', $hashedPassword);

													$json['Success'] = true;
													$json['Redirect'] = $settings->getSitePath() . '/account';
													unset($json['Message']);
												} else {
													$json['Message'] = $this->getLanguage()->json()['Register']['Error7'];
												}
											} else {
												$json['Message'] = $this->getLanguage()->json()['Register']['Error6'];
											}
										} else {
											$json['Message'] = $this->getLanguage()->json()['Register']['Error5'];
										}
									} else {
										$json['Message'] = $this->getLanguage()->json()['Register']['Error4'];
									}
								} else {
									$json['Message'] = $this->getLanguage()->json()['Register']['Error3'];
								}
							} else {
								$json['Message'] = $this->getLanguage()->json()['Register']['Error2'];
							}
						}
					}

					echo json_encode($json, JSON_PRETTY_PRINT);
				} else if($route->getData() == 'register') {
					# Register
					$this->redirectBy(Redirects::USER_LOGGED_IN, 'account');
					$tpl = $this->createView('Register.tpl', 'registerView');

					if(isset($_POST['username'], $_POST['password'], $_POST['password_confirm'])) {
						$tpl->post_username = $_POST['username'];

						if(strlen($_POST['username']) && strlen($_POST['password']) && strlen($_POST['password_confirm'])) {
							$userFactory = $this->getControllerParameters()->getModelsManager()->get(UserFactory::class);
							$user = $userFactory->getByColumn('username', $_POST['username']);

							if($user == null) {
								if(strlen($_POST['username']) >= 3) {
									if(strlen($_POST['username']) <= 16) {
										if(Common::isValidUsername($_POST['username'])) {
											if(strlen($_POST['password']) >= 6) {
												if($_POST['password'] === $_POST['password_confirm']) {
													$hashedPassword = $userFactory->getHashedString($_POST['password_confirm']);

													$registeredUser = $userFactory->_create([
														'username' => $_POST['username'],
														'password' => $hashedPassword
													]);

													$this->setSession('USERID', (string) $registeredUser->getRow()->id);
													$this->setSession('PASSWORD', $hashedPassword);

													Common::redirect('account');
												} else {
													$tpl->text = $this->getLanguage()->json()['Register']['Error7'];
												}
											} else {
												$tpl->text = $this->getLanguage()->json()['Register']['Error6'];
											}
										} else {
											$tpl->text = $this->getLanguage()->json()['Register']['Error5'];
										}
									} else {
										$tpl->text = $this->getLanguage()->json()['Register']['Error4'];
									}
								} else {
									$tpl->text = $this->getLanguage()->json()['Register']['Error3'];
								}
							} else {
								$tpl->text = $this->getLanguage()->json()['Register']['Error2'];
							}
						} else {
							$tpl->text = $this->getLanguage()->json()['Register']['Error1'];
						}
					}

					$this->displayView($tpl);
				}
	        }

	        public function getRoutes(): array {
	            return [
		            new Route('/register/post', $this, 'registerJsonResponse'),
		            new Route('/register', $this, 'register')
	            ];
	        }
	    }
	}
