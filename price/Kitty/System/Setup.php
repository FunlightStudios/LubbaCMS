<?php
	/**
	 * @author Candan Tombas
	 */

	use Kitty\System\Common;
	use Kitty\System\Application;
	use Kitty\System\Configuration;

	use Kitty\System\Configurations\Database;
	use Kitty\System\Configurations\Paths;

	use Kitty\System\Utils\Helpers\Multilingual\Multilingual;
	use Kitty\System\Utils\Helpers\Templates\TplManager;

	use Kitty\System\Utils\Routing\Router;
	use Kitty\System\Utils\Routing\Managers\RouteManager;
	use Kitty\System\Utils\Routing\Collectors\RouteCollector;
	use Kitty\System\Utils\Routing\RouterResult;
	use Kitty\System\Utils\Routing\Interfaces\IRoute;
	use Kitty\System\Utils\Routing\RouteInfoResults;

	use Kitty\System\Patterns\MVC\Controllers\Collectors\ControllerCollector;
	use Kitty\System\Patterns\MVC\Controllers\ControllerParameters;

	use Kitty\System\Patterns\MVC\Templates\Managers\TemplateManager;
	use Kitty\System\Patterns\MVC\Templates\View;

	use Kitty\System\Patterns\MVC\Models\Handlers\ModelsManager;

	spl_autoload_register(function($className) {
		$classNameSplit = explode('\\', $className);
		$class = implode('/', $classNameSplit);
		if(file_exists($class . '.php')) {
	    	require_once $class . '.php';
		}
 	});

	$configuration = new Configuration($configuration);
	$database = new Database($configuration->getDatabaseSettings());
	$paths = new Paths($configuration->getPaths());

	# Creates database connection
	$connection = new PDO(
		$database->getString(),
		$database->getUsername(),
		$database->getPassword()
	);

	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
	$connection->exec('set names utf8');
	mb_internal_encoding('UTF-8');

	require_once $paths->getLibrariesPath() . '/Smarty/Autoloader.php';
	Smarty_Autoloader::registerBC();
	require_once $paths->getLibrariesPath() . '/Smarty/SmartyBC.class.php';

	$modelsManager = new ModelsManager($connection);
	Common::init($configuration, $modelsManager);

	# Loads languages
	$language = Common::getSettingsFactory()->getLanguage();
	$multilingual = new Multilingual($paths, $language, $configuration->getCountryAutoDetect());

	# Loads templates
	$tplManager = new TplManager($paths, Common::getSettingsFactory()->getTemplate());

	# Creates main objects
	$application = new Application($configuration, $connection, $multilingual, $tplManager);
	$smarty = new Smarty();
	$routeCollector = new RouteCollector();
	$controllerCollector = new ControllerCollector();
	$templateManager = new TemplateManager($paths->getTemplatesPath(), $smarty);

	$controllerParameters = new ControllerParameters(
		$application,
		$controllerCollector,
		$templateManager,
		new View(),
		$modelsManager
	);

	# Loads controllers
	$controllers = scandir($paths->getControllersPath());
	foreach($controllers as $key => $value) {
		if(strpos(strtolower($value), '.php') !== false) {
			$path = pathinfo($value);

			require_once $paths->getControllersPath() . '/' . $path['basename'];
			$controller = '\\Application\\Controllers\\' . $path['filename'];
			$controllerCollector->add(new $controller($controllerParameters));
		}
	}

	foreach($controllerCollector->getByInstance(IRoute::class) as $controller) {
	    foreach($controller->getRoutes() as $route) {
	        $routeCollector->add($route);
	    }
	}

	$routeManager = new RouteManager($routeCollector);
	$router = new Router($routeManager);

	try {
	    $connection->beginTransaction();
	    $connection->query("SET NAMES 'utf8'");

	    $router->listenOn($router->getURLParameter('r'), function(RouterResult $routerResult) {
	        switch($routerResult->getState()) {
	            case RouteInfoResults::ACCESS_ALLOWED:
	                return $routerResult->callController();
	                break;

	            case RouteInfoResults::NOT_FOUND:
	                return Common::redirect('error');

	            break;
	        }
	    });

	    $connection->commit();
	} catch(Exception $ex) {
	    $connection->rollBack();
	}
