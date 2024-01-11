<?php
	/**
	 * @author Candan Tombas
	 */

	namespace Kitty\System\Utils\Routing\Helpers {
		use Kitty\System\Common;

	    final class RouteHelper {

			/**
			 * @param 	string $url
			 * @return 	string
			 */
	        public static function validateUrl(string $url): string {
	            $_url = '';

	            foreach(explode('/', $url) as $urlPart) {
	                if(!empty($urlPart)) {
	                    $_url .= '/' . $urlPart;
	                }
	            }

	            return $_url;
	        }

			/**
			 * @param 	string 	$url
			 * @param 	string 	$pattern
			 * @return 	array
			 */
	        public static function compareURLtoPattern(string $url, string $pattern): array {
	            $splitURL = explode('/', $url);
	            $splitPattern = explode('/', $pattern);

	            $compareData = [
					'int' 		=> 0,
					'string' 	=> 0,
					'boolean' 	=> 0,
					'equal' 	=> 0,
					'base64'	=> 0,
					'float' 	=> 0
				];

	            $return = ['parameters' => [], 'matches' => false];

	            if(count($splitURL) == count($splitPattern)) {
	                for($i = 0; $i < count($splitURL); ++$i) {
	                    if($splitURL[$i] == $splitPattern[$i]) {
	                        $compareData['equal']++;
	                    } else {
	                        if(substr($splitPattern[$i], 0, 1) == '{' && substr($splitPattern[$i], -1) == '}') {
	                            $commandInfo = explode(':', substr($splitPattern[$i], 1, strlen($splitPattern[$i]) - 2));
	                            if(count($commandInfo) == 2) {
									if($commandInfo[0] == 'float') {
										if(Common::isDecimal($splitURL[$i])) {
											$compareData['float']++;
											$return['parameters'][] = ['name' => $commandInfo[1], 'value' => (float) $splitURL[$i]];
										}
									} else if($commandInfo[0] == 'base64') {
										if(base64_encode(base64_decode($splitURL[$i], true)) === $splitURL[$i]) {
											$compareData['base64']++;
											$return['parameters'][] = ['name' => $commandInfo[1], 'value' => base64_decode($splitURL[$i])];
										}
									} else if($commandInfo[0] == 'bool' || $commandInfo[0] == 'boolean') {
	                                    if(strtolower($splitURL[$i]) == 'true' || strtolower($splitURL[$i]) == 'false') {
	                                        $compareData['boolean']++;
	                                        $return['parameters'][] = ['name' => $commandInfo[1], 'value' => strtolower($splitURL[$i]) == "true" ? true : false];
	                                    }
	                                } else if($commandInfo[0] == 'string') {
	                                    $compareData['string']++;
	                                    $return['parameters'][] = ['name' => $commandInfo[1], 'value' => $splitURL[$i]];
	                                } else if($commandInfo[0] == 'int' || $commandInfo[0] == 'integer') {
	                                    if(ctype_digit($splitURL[$i])) {
	                                        $compareData['int']++;
	                                        $return['parameters'][] = ['name' => $commandInfo[1], 'value' => (int) $splitURL[$i]];
	                                    }
	                                }
	                            }
	                        }
	                    }

	                    if(($compareData['int'] + $compareData['string'] + $compareData['boolean'] + $compareData['equal']) == count($splitURL)) {
	                        $return['matches'] = true;
	                        return $return;
	                    }
	                }
	            }

	            return $return;
	        }

			/**
			 * @param 	string $key
			 * @return 	string
			 */
	        public static function getUrl(string $key): string {
	            if(isset($_GET[$key])) {
	                return self::validateUrl($_GET[$key]);
	            } else {
	                return '/';
	            }
	        }
	    }
	}
