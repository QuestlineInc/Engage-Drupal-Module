<?php
namespace Drupal\questline_engage\Core;

class EngageCommon {
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
		\Drupal::logger('questline_engage')->error($message, $data);
	}
}
