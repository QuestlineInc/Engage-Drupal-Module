<?php
namespace Drupal\questline_engage\Core;

class EngageApi {
	public function getArticleEmbed($article_id = '', $article_type = '') {
		$common = new \Drupal\questline_engage\Core\EngageCommon();
		$error_msg = 'Error retrieving Engage article for embed code; article @id, type @type (@error_info)';
		
		$embed = '';
		
		if ($article_id != '' && $article_type != '') {
			$url = QL_ENGAGE_API_URL . '/content/articles/' . $article_type . '/' . $article_id . '?expand=embed';
			$api_result = $this->makeApiGetCall($url);
			
			if (!$api_result['Error']) {
				if ($api_result['Info']['http_code'] == 200) {
					$decoded = json_decode($api_result['Response']);
					
					if ($decoded->Error == null) {
						$embed = json_encode($decoded->Article->Embed);
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
		$common = new \Drupal\questline_engage\Core\EngageCommon();
		
		$article = null;
		
		if ($article_id != '' && $article_type != '') {
			$url = QL_ENGAGE_API_URL . '/content/articles/' . $article_type . '/' . $article_id . '?format=html';
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
		$common = new \Drupal\questline_engage\Core\EngageCommon();
		
		$results = null;
		
		$url = QL_ENGAGE_API_URL . '/content/articles?search=' . urlencode($keyword) . '&page=index~' . $page_index . ',size~' . $page_size;
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
	
	private function makeApiGetCall($url) {
		$api_key = \Drupal::config('questline_engage.admin_apikey')->get('questline_engage_apikey');
		$headers = array('Authorization: Basic ' . base64_encode($api_key));
		
		$http_client = curl_init($url);
		curl_setopt($http_client, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($http_client, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($http_client, CURLOPT_TIMEOUT, 60);
		curl_setopt($http_client, CURLOPT_RETURNTRANSFER, true);
		
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
}
