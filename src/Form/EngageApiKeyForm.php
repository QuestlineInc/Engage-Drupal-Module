<?php
namespace Drupal\questline_engage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EngageApiKeyForm extends ConfigFormBase {
	private $_config_name = 'questline_engage.admin_apikey';
	private $_form_id = 'questline_engage_apikey_form';
	private $_field_name = 'questline_engage_apikey';
	
	public function getEditableConfigNames() {
		return [$this->_config_name];
	}
	
	public function getFormId() {
		return $this->_form_id;
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
