<?php
namespace Drupal\questline_engage\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginInterface;
use Drupal\ckeditor\CKEditorPluginButtonsInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;

/**
 * @CKEditorPlugin(
 *    id = "EngageArticleSearch",
 *    label = "Questline Engage Article Search"
 * )
 */
class EngageArticleSearch extends PluginBase implements CKEditorPluginInterface, CKEditorPluginButtonsInterface {
	public function isEnabled(Editor $editor) {
		return true;
	}

	// Implements CKEditorPluginInterface::isInternal()
	public function isInternal() {
		return false;
	}

	// Implements CKEditorPluginInterface::getConfig()
	public function getConfig(Editor $editor) {
		return array();
	}
	
	// Implements CKEditorPluginInterface::getDependencies
	public function getDependencies(Editor $editor) {
		return array();
	}
	
	// Implements CKEditorPluginInterface::getFile()
	public function getFile() {
		return drupal_get_path('module', 'questline_engage') . '/js/plugins/engagearticlesearch/plugin.js';
	}
	
	// Implements CKEditorPluginInterface::getLibraries()
	public function getLibraries(Editor $editor) {
		return array();
	}

	// Implements CKEditorPluginButtonsInterface::getButtons()
	public function getButtons() {
		$icon = drupal_get_path('module', 'questline_engage') . '/js/plugins/engagearticlesearch/images/engage-16.png';
		
		return array(
			'EngageArticleSearch' => array(
				'label' => 'Engage Article Search',
				'image' => $icon
			)
		);
	}
}
