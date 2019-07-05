<?php  
/**  
 * @file  
 * Contains Drupal\form_node_redirect\Form\RedirectConfigForm.  
 */  
namespace Drupal\form_node_redirect\Form; 


use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface; 
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  

/**
 * config form for content redirect to custom internal path.
 *
 * @internal
 */
class NodeRedirectConfigForm extends ConfigFormBase {  
  
  /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'from_node_redirect.adminsettings',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'form_node_redirect_admin_form';  
  }

  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    
    $config = $this->config('from_node_redirect.adminsettings');  
    $node_types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('node');

    $form['label']  = array(
      '#type' => 'label',
      '#title' => $this->t('<strong>Enter redirect paths for following content types</strong>'),
      '#id'         => 'lbl1',
      '#prefix'     => '<div class="caption1">',
      '#suffix'     => '</div>',
    );

    foreach ($node_types as $key => $value) {
      # code...
      
      $form[$key] = [
        '#type' => 'textfield',
        '#title' => $this->t($value['label']),
        '#default_value' => $config->get($key),
        '#required' => FALSE,
      ]; 
    } 

    return parent::buildForm($form, $form_state);  
  }

  /**  
   * {@inheritdoc}  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    parent::submitForm($form, $form_state);  
    $form_values = $form_state->getValue(array());
    $type_array = [];
    
    foreach ($form_values as $key => $value) {
      # code...
      if ($key == 'submit') {
        break;
      }
      \Drupal::service('config.factory')->getEditable('from_node_redirect.adminsettings')->set($key , $value)->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $form_values = $form_state->getValue(array());

    foreach ($form_values as $key => $value) {
      # code...
      $url_object = \Drupal::service('path.validator')->getUrlIfValid($value);
      if ($key == 'submit') {
        break;
      }
      if (mb_substr($value, 0, 1) !== '/') {
        $form_state->setErrorByName($value , t(' The path should start with /.'));
      }
      if ($url_object == FALSE) {
        $form_state->setErrorByName($value , t(' The path ' . $value . ' is not a valid internal path.'));  
      }
    }
  }  
}  
