<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Patterns\MVC\Controllers {
	    use Kitty\System\App;
		use Kitty\System\Common;
		use Kitty\System\Utils\Helpers\Redirects\Redirects;
		use Kitty\System\Utils\Helpers\Templates\TplManager;
		use Kitty\System\Utils\Helpers\Multilingual\Language;

		use Kitty\System\Patterns\MVC\Templates\Template;
		use Kitty\System\Patterns\MVC\Templates\Managers\TemplateManager;

		use Application\Models\User\UserFactory;
		use Application\Models\Settings\SettingsFactory;

	    abstract class Controller {
	        private $controllerParameters;
			private $session = null;

			/**
			 * @param ControllerParameters $controllerParameters
			 */
	        public function __construct(ControllerParameters $controllerParameters) {
	            $this->controllerParameters = $controllerParameters;
	        }

			/**
			 * @return ControllerParameters
			 */
	        public function getControllerParameters(): ControllerParameters {
	            return $this->controllerParameters;
	        }

			/**
			 * @return TemplateManager
			 */
			public function getTplManager(bool $refresh = false): TplManager {
				return $this->getControllerParameters()->getApplication()->getTplManager();
			}

			/**
			 * @param string $session
			 * @param mixed $content
			 */
			public function setSession(string $session, $content) {
				$_SESSION[$this->getControllerParameters()->getApplication()->getConfiguration()->getSessionPrefix() . $session] = $content;
			}

			/**
			 * @param  string $session
			 * @return mixed
			 */
			public function getSession(string $session) {
				if(isset($_SESSION[$this->getControllerParameters()->getApplication()->getConfiguration()->getSessionPrefix() . $session])) {
					return $_SESSION[$this->getControllerParameters()->getApplication()->getConfiguration()->getSessionPrefix() . $session];
				}

				return null;
			}

			/**
			 * @param string $file
 			 * @param string $pageId
			 */
			public function createView(string $file, string $pageId = ''): Template {
				$settings = $this->getControllerParameters()->getModelsManager()->get(SettingsFactory::class);
				$templateDir = $this->getTplManager()->getTemplate($settings->getTemplate())->getDirectoryName();

				$file = $templateDir . '/' . $file;
				$tpl = $this->getControllerParameters()->getTemplateManager()->make($file);

				$tpl->settings = $settings;
				$tpl->pageId = $pageId;
				$tpl->this = $this;

				return $tpl;
			}

			/**
			 * @param Template $tpl
			 */
			public function displayView(Template $tpl) {
				$this->getControllerParameters()->getView()->display($tpl);
			}

			/**
			 * @param int 	$type
 			 * @param string 	$url
			 */
			public function redirectBy(int $type, string $url) {
				if($type == Redirects::USER_NOT_LOGGED_IN && $this->getUser() == null) {
					Common::redirect($url);
				} else if($type == Redirects::USER_LOGGED_IN && $this->getUser() != null) {
					Common::redirect($url);
				}
			}

			/**
			 * @return User
			 */
			public function getUser(bool $refresh = false) {
				if($this->getUserSession($refresh) != null) {
					if($this->getSession('USERID') != null && $this->getSession('PASSWORD') != null) {
						if($this->getUserSession($refresh)->getRow()->password === $this->getSession('PASSWORD')) {
							return $this->getUserSession($refresh);
						}
					}
				}

				return null;
			}

			/**
			 * @return Language
			 */
			public function getLanguage(): Language {
				$language = 'DEFAULT';
				if($this->getUser() != null) {
					$language = $this->getUser()->getRow()->language;
				} else if($this->getSession('LANGUAGE') != null) {
					$language = $this->getSession('LANGUAGE');
				}

				return $this->getControllerParameters()->getApplication()->getMultilingual()->getLanguage($language);
			}

			/**
			 * @param bool $refresh
			 * @return null|User
			 */
			public function getUserSession(bool $refresh = false) {
				if((gettype($this->session) != 'object' && $this->session == null) || $refresh) {
					if($this->getSession('USERID')) {
						$userFactory = $this->getControllerParameters()->getModelsManager()->get(UserFactory::class);
						$user = $userFactory->getByColumn('id', $this->getSession('USERID'));
						if($user != null) {
							$this->session = $user;
						}
					} else {
						$this->session = null;
					}
				}

				return $this->session;
			}
	    }
}
