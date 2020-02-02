<?php
namespace Drupal\questline_engage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EngageApiKeyForm extends ConfigFormBase {

    const SETTINGS = 'questline_engage_custom.settings';

    public function getEditableConfigNames() {
        return [
            static::SETTINGS,
        ];
    }

    public function getFormId() {
        return $this->configFactory->get('questline_engage_custom.settings')->get('form_id');
    }

    public function getApiKey() {
        return $this->configFactory->get('questline_engage_custom.settings')->get('api_key');
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $form["api_key_field"] = [
            '#type' => 'textfield',
            '#title' => 'API Key',
            '#description' => 'Enter your API key to access Questline Engage content',
            '#default_value' => $this->getApiKey()
        ];

        return parent::buildForm($form, $form_state);
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {

        $this->configFactory->getEditable(static::SETTINGS)
            ->set('api_key', $form_state->getValue("api_key_field"))
            ->save();

        parent::submitForm($form, $form_state);
    }
}
