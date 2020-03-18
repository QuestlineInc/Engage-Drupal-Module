<?php
namespace Drupal\questline_engage;


class EngageApi{
    protected $ql_base_url;
    protected $configFactory;
    public function __construct() {
        $this->configFactory = \Drupal::config("questline_engage_custom.settings");
        //TODO: Replace with proper config
        $this->ql_base_url = 'https://api.questline.com/v3';
    }
	public function getArticleEmbed($article_id = '', $article_type = '') {
		$common = new EngageCommon();
		$error_msg = t('Error retrieving Engage article for embed code; article @id, type @type (@error_info)');
		
		$embed = '';
		
		if ($article_id != '' && $article_type != '') {
            //TODO: replace wih proper config

		    $url = $this->ql_base_url . '/content/articles/' . $article_type . '/' . $article_id . '?expand=embed';
			$api_result = $this->makeApiGetCall($url);
			if (!$api_result['Error']) {
				if ($api_result['Info']['http_code'] == 200) {
					$decoded = json_decode($api_result['Response']);
					
					if ($decoded->Error == null) {
						$embed = json_encode($decoded->Article->Embed);
						$embed = $this->stripSpecialCharsSlashesQuotes($embed);
					}
					else {
						$common->logError($error_msg, array('@id' => $article_id, '@type' => $article_type, '@error_info' => $decoded->Error));
					}
				}
				else {
					$common->logError($error_msg, array('@id' => $article_id, '@type' => $article_type, '@error_info' => $api_result['Info']));
				}
			}
			else {
				$common->logError($error_msg, array('@id' => $article_id, '@type' => $article_type, '@error_info' => $api_result['Error']));
			}
		}
		
		return $embed;
	}
	
	public function getArticlePreview($article_id = '', $article_type = '') {
		$common = new EngageCommon();
		$article = null;
		if ($article_id != '' && $article_type != '') {
			$url = $this->ql_base_url . '/content/articles/' . $article_type . '/' . $article_id . '?format=html';
			$api_result = $this->makeApiGetCall($url);
			if (!$api_result['Error']) {
				if ($api_result['Info']['http_code'] == 200) {
					$decoded = json_decode($api_result['Response']);

					if ($decoded->Error == null) {
						$article = $decoded->Article;
					}
					else {
						$common->logError('Error retrieving Engage article for preview (@error_info)', array('@error_info' => $decoded->Error));
					}
				}
				else {
					$common->logError('Error retrieving Engage article for preview (@error_info)', array('@error_info' => $api_result['Info']));
				}
			}
			else {
				$common->logError('Error retrieving Engage article for preview (@error_info)', array('@error_info' => $api_result['Error']));
			}
		}
		
		return $article;
	}
	
	public function searchArticles($keyword = '', $page_index = 0, $page_size = 10) {
		$common = new EngageCommon();
		
		$results = null;
		$url = $this->ql_base_url . '/content/articles?search=' . urlencode($keyword) . '&page=index~' . $page_index . ',size~' . $page_size;
		$api_result = $this->makeApiGetCall($url);
		if (!$api_result['Error']) {
			if ($api_result['Info']['http_code'] == 200) {
				$decoded = json_decode($api_result['Response']);
				
				if ($decoded->Error == null) {
					$results = array();
					$results['Articles'] = $decoded->Articles;
					$results['PageIndex'] = $decoded->PageIndex;
					$results['PageSize'] = $decoded->PageSize;
					$results['TotalPages'] = $decoded->TotalPages;
					$results['TotalResults'] = $decoded->TotalResults;
				}
				else {
					$common->logError('Error searching Engage articles (@error_info)', array('@error_info' => $decoded->Error));
				}
			}
			else {
				$common->logError('Error searching Engage articles (@error_info)', array('@error_info' => $api_result['Info']));
			}
		}
		else {
			$common->logError('Error searching Engage articles (@error_info)', array('@error_info' => $api_result['Error']));
		}
		
		return $results;
	}

    public function getApiKey() {
        return $this->configFactory->get('api_key');
    }

	private function makeApiGetCall($url) {
		$api_key = $this->getApiKey();
		$headers = array('Authorization: Basic ' . base64_encode($api_key));

		$http_client = curl_init($url);
		curl_setopt($http_client, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($http_client, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($http_client, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($http_client, CURLOPT_TIMEOUT, 60);
		curl_setopt($http_client, CURLOPT_RETURNTRANSFER, true);
		//NOTE: this should be removed after the SSL issue is resolved! But have to do this in test to get a result.
		curl_setopt($http_client, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($http_client);
		$info = curl_getinfo($http_client);
		$error = curl_error($http_client);
		curl_close($http_client);

		$result = array();
		$result['Response'] = $response;
		$result['Info'] = $info;
		$result['Error'] = $error;

		return $result;
	}
	
	private function stripSpecialCharsSlashesQuotes($string) {
		$new_string = $string;
		
		// Strip special chars for tabs, returns, and newlines
		$new_string = str_replace('\\t', '', $new_string);
		$new_string = str_replace('\\r', '', $new_string);
		$new_string = str_replace('\\n', '', $new_string);
		
		// Strip any remaining slashes
		$new_string = stripslashes($new_string);
		
		// Trim any double quotes so that the value of the string itself
		// doesn't contain any beginning or ending double quotes
		$new_string = trim($new_string, '"');
		
		return $new_string;
	}
}
