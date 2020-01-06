<?php

namespace Drupal\movies\Form;
use Drupal\Core\Form\ConfigFormBase;



class PagingConfiguration extends ConfigFormBase
{

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'movie.settings';


  /**
   * @inheritDoc
   */
  protected function getEditableConfigNames()
  {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * @inheritDoc
   */
  public function getFormId()
  {
    return 'movie_admin_settings';
  }



  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['movies_per_page'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Results per page'),
      '#default_value' => $config->get('movies_per_page'),
    ];

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('movies_per_page', $form_state->getValue('movies_per_page'))
      // You can set multiple configurations at once by making
      // multiple calls to set().
      //->set('other_things', $form_state->getValue('other_things'))
      ->save();

    parent::submitForm($form, $form_state);
  }



}
