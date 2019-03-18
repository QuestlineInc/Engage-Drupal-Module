<?php
namespace Drupal\questline_engage\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * @Filter(
 *    id = "questline_engage_article_shortcode",
 *    title = "Questline Engage article shortcodes",
 *    description = "Embeds articles from Questline's Engage platform into your content.",
 *    type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE
 * )
 */
class ArticleShortcode extends FilterBase {
	public function process($text, $langcode) {
		$pattern = '/\[ql_engage_article.+?\/\]/s';
		$count = preg_match_all($pattern, $text, $matches);

		if ($count > 0) {
			$new_text = $text;
			
			for ($i = 0; $i < $count; $i++) {
				$shortcode = $matches[0][$i];
				$kvps = $this->splitShortcodeIntoKeyValuePairs($shortcode);

				// Get shortcode param values
				$article_id = $this->getKeyValueFromArray('id', $kvps);
				$article_type = $this->getKeyValueFromArray('type', $kvps);
				$display_title = $this->getKeyValueFromArray('display_title', $kvps);
				$display_published_date = $this->getKeyValueFromArray('display_published_date', $kvps);
				
				// Call out to Engage API to retrieve article
				$article_embed = $this->getArticleEmbed($article_id, $article_type);
				
				// Once we have the article embed code, strip the appropriate chars and add
				// additional css to hide article title and/or published date, if defined.
				$article_embed = $this->stripSpecialCharsSlashesQuotes($article_embed);
				$article_embed .= $this->additionalCss($article_id, $display_title, $display_published_date);
				
				// Now replace the article shortcode with the markup for the article itself
				$new_text = str_replace($shortcode, $article_embed, $new_text);
			}

			return new FilterProcessResult($new_text);
		}
		else {
			// Just return what was there
			return new FilterProcessResult($text);
		}
	}
	
	public function settingsForm(array $form, FormStateInterface $form_state) {
		$form['questline_engage_article_shortcode_display_titles'] = array(
			'#type' => 'checkbox',
			'#title' => 'Display article titles',
			'#description' => 'Determines whether or not to show the Engage article title in the embedded article HTML.',
			'#default_value' => $this->settings['questline_engage_article_shortcode_display_titles']
		);
		
		$form['questline_engage_article_shortcode_display_published_dates'] = array(
			'#type' => 'checkbox',
			'#title' => 'Display published dates',
			'#description' => 'Determines whether or not to show the Engage article published date in the embedded article HTML.',
			'#default_value' => $this->settings['questline_engage_article_shortcode_display_published_dates']
		);
		
		return $form;
	}
	
	private function additionalCss($article_id, $display_title, $display_published_date) {
		$settings_display_titles = $this->settings['questline_engage_article_shortcode_display_titles'];
		$settings_display_published_dates = $this->settings['questline_engage_article_shortcode_display_published_dates'];
		
		$css_hide_title = '#ql-embed-' . $article_id . ' h1.ql-embed-article__title { display: none; }';
		$css_hide_published_date = '#ql-embed-' . $article_id . ' p.ql-embed-article__pubdate { display: none; }';
		
		$css = '<style type="text/css">';
		
		// Check to hide display title. If the display_title param was given
		// in the shortcode, use it; otherwise, use the filter setting
		if ($display_title != null) {
			if ($display_title == 'false') {
				$css .= $css_hide_title;
			}
		}
		else {
			if ($settings_display_titles == '0') {
				$css .= $css_hide_title;
			}
		}
		
		// Check to hide display published date. If the display_published_date param
		// was given in the shortcode, use it; otherwise, use the filter setting
		if ($display_published_date != null) {
			if ($display_published_date == 'false') {
				$css .= $css_hide_published_date;
			}
		}
		else {
			if ($settings_display_published_dates == '0') {
				$css .= $css_hide_published_date;
			}
		}
		
		$css .= '</style>';
		
		return $css;
	}
	
	private function getArticleEmbed($article_id = '', $article_type = '') {
		$embed = '';
		
		if ($article_id != '' && $article_type != '') {
			$url = 'http://api-local.questline.com/v3/content/articles/' . $article_type . '/' . $article_id . '?expand=embed';

			$http_client = $this->httpGet($url);
			$response = curl_exec($http_client);
			curl_close($http_client);
			
			$decoded = json_decode($response);
			$embed = json_encode($decoded->Article->Embed);
		}
		
		return $embed;
	}

	private function httpGet($url) {
		$api_key = \Drupal::config('questline_engage.admin_apikey')->get('questline_engage_apikey');
		$headers = array('Authorization: Basic ' . base64_encode($api_key));
		
		$http_client = curl_init($url);
		curl_setopt($http_client, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($http_client, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($http_client, CURLOPT_TIMEOUT, 60);
		curl_setopt($http_client, CURLOPT_RETURNTRANSFER, true);
		
		return $http_client;
	}
	
	private function contains($haystack, $needle)
	{
		return strpos($haystack, $needle) !== false;
	}
	
	private function stripSpecialCharsSlashesQuotes($string) {
		// Strip special chars for tabs, returns, and newlines
		$string = str_replace('\\t', '', $string);
		$string = str_replace('\\r', '', $string);
		$string = str_replace('\\n', '', $string);
		
		// Strip any remaining slashes
		$string = stripslashes($string);
		
		// Trim any double quotes so that the value of the string itself
		// doesn't contain any beginning or ending double quotes
		$string = trim($string, '"');
		
		return $string;
	}
	
	private function getKeyValueFromArray($key, $array) {
		$value = null;
		
		foreach ($array as $k => $v) {
			if ($k == $key) {
				$value = $v;
				break;
			}
		}
		
		return $value;
	}
	
	private function splitShortcodeIntoKeyValuePairs($shortcode) {
		$kvps = array();
		
		// First split shortcode on single space char
		$parts = explode(' ', $shortcode);
		
		foreach ($parts as $item) {
			if ($this->contains($item, '=')) {
				// Then split on equal sign
				$tmp = explode('=', $item);
				$kvp = array();
				
				// Trim the double quotes so that the value of the string itself
				// doesn't contain any beginning or ending double quotes
				$kvp[$tmp[0]] = trim($tmp[1], '"');
				
				// Merge the array into the parent
				$kvps = array_merge($kvps, $kvp);
			}
		}
		
		return $kvps;
	}
}
