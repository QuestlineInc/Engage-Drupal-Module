<?php
namespace Drupal\questline_engage\Core;
use Drupal\questline_engage\Config;

class EngageCommon {

    protected $loggerFactory;
    public function __construct() {
        $this->loggerFactory = \Drupal::logger('questline_engage');
    }
    public function contains($haystack, $needle)
	{
		return strpos($haystack, $needle) !== false;
	}
	
	public function getKeyValueFromArray($key, $array) {
		$value = null;
		
		foreach ($array as $k => $v) {
			if ($k == $key) {
				$value = $v;
				break;
			}
		}
		
		return $value;
	}
	
	public function logError($message, $data) {
		// Puts an error record in the Drupal watchdog table
		$this->loggerFactory->error($message, $data);
	}
}
