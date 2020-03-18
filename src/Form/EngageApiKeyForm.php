<?php
namespace Drupal\questline_engage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EngageApiKeyForm extends ConfigFormBase {
	private $_config_name = 'questline_engage.settings';
	private $_field_name = 'api_key';
	
	public function getEditableConfigNames() {
		return [$this->_config_name];
	}
	
	public function getFormId() {
		return 'questline_engage_admin_settings';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state) {
		$config = $this->config($this->_config_name);
		
		$form[$this->_field_name] = [
			'#type' => 'textfield',
			'#title' => 'API Key',
			'#description' => 'Enter your API key to access Questline Engage content',
			'#default_value' => $config->get($this->_field_name)
		];
		
		return parent::buildForm($form, $form_state);
	}
	
	public function validateForm(array &$form, FormStateInterface $form_state) {
		parent::validateForm($form, $form_state);
	}
	
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$this->config($this->_config_name)
			 ->set($this->_field_name, $form_state->getValue($this->_field_name))
			 ->save();
			 
		parent::submitForm($form, $form_state);
	}
}
