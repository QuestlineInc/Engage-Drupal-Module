<?php
namespace Drupal\questline_engage\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\questline_engage\EngageApi;
use Drupal\questline_engage\EngageCommon;

/**
 * @Filter(
 *    id = "questline_engage_article_shortcode",
 *    title = "Questline Engage article shortcodes",
 *    description = "Embeds articles from Questline's Engage platform into your content.",
 *    type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE
 * )
 */
class EngageArticleShortcode extends FilterBase {
	public function process($text, $langcode) {
		$pattern = '/\[ql_engage_article.+?\/\]/s';
		$count = preg_match_all($pattern, $text, $matches);

		if ($count > 0) {
			$new_text = $text;
			
			for ($i = 0; $i < $count; $i++) {
				$shortcode = $matches[0][$i];
				$kvps = $this->splitShortcodeIntoKeyValuePairs($shortcode);

				// Get shortcode param values
				$common = new EngageCommon();
				$article_id = $common->getKeyValueFromArray('id', $kvps);
				$article_type = $common->getKeyValueFromArray('type', $kvps);
				$display_title = $common->getKeyValueFromArray('display_title', $kvps);
				$display_published_date = $common->getKeyValueFromArray('display_published_date', $kvps);
				$include_jquery = $common->getKeyValueFromArray('include_jquery', $kvps);
				
				// Check to include jquery
				$article_embed = $this->includeJQuery($include_jquery);
				
				// Call out to Engage API to retrieve article
				$api = new EngageApi();
				$article_embed .= $api->getArticleEmbed($article_id, $article_type);

				// Add additional css to hide article title and/or published date
				$article_embed .= $this->hideTitleAndOrPublishedDate($article_id, $display_title, $display_published_date);
				
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
			'#title' => $this->t('Display article titles'),
			'#description' => $this->t('Determines whether or not to show the Engage article title in the embedded article HTML.'),
			'#default_value' => $this->settings['questline_engage_article_shortcode_display_titles']
		);
		
		$form['questline_engage_article_shortcode_display_published_dates'] = array(
			'#type' => 'checkbox',
			'#title' => $this->t('Display published dates'),
			'#description' => $this->t('Determines whether or not to show the Engage article published date in the embedded article HTML.'),
			'#default_value' => $this->settings['questline_engage_article_shortcode_display_published_dates']
		);
		
		$form['questline_engage_article_shortcode_include_jquery'] = array(
			'#type' => 'checkbox',
			'#title' => $this->t('Include jQuery'),
			'#description' => $this->t('Determines whether or not to include jQuery in the embedded article HTML. Check this if your theme does not use jQuery.'),
			'#default_value' => $this->settings['questline_engage_article_shortcode_include_jquery']
		);
		
		return $form;
	}
	
	private function includeJQuery($include_jquery) {
		$settings_include_jquery = $this->settings['questline_engage_article_shortcode_include_jquery'];
		$jquery_src = \Drupal::request()->getSchemeAndHttpHost() . '/' . drupal_get_path('module', 'questline_engage') . '/js/jquery-3.3.1.min.js';
		$jquery_script = '<script type="text/javascript" src="' . $jquery_src . '"></script>';
		
		$output = '';
		
		// Check to add jquery to the output. If the include_jquery param was given
		// in the shortcode, use it; otherwise, use the filter setting
		if ($include_jquery != null) {
			if ($include_jquery == 'true') {
				$output = $jquery_script;
			}
		}
		else {
			if ($settings_include_jquery == '1') {
				$output = $jquery_script;
			}
		}
		
		return $output;
	}
	
	private function hideTitleAndOrPublishedDate($article_id, $display_title, $display_published_date) {
		$settings_display_titles = $this->settings['questline_engage_article_shortcode_display_titles'];
		$settings_display_published_dates = $this->settings['questline_engage_article_shortcode_display_published_dates'];
		
		$css_hide_title = '#ql-embed-' . $article_id . ' h1.ql-embed-article__title { display: none; }';
		$css_hide_published_date = '#ql-embed-' . $article_id . ' p.ql-embed-article__pubdate { display: none; }';
		
		$css = '<style type="text/css">';
		
		// Check to hide article title. If the display_title param was given
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
		
		// Check to hide article published date. If the display_published_date param
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
	
	private function splitShortcodeIntoKeyValuePairs($shortcode) {
		$kvps = array();
		$common = new EngageCommon();
		
		// First split shortcode on single space char
		$parts = explode(' ', $shortcode);
		
		foreach ($parts as $item) {
			if ($common->contains($item, '=')) {
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
