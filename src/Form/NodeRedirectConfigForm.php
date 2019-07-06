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
use Drupal\form_node_redirect\FormNodeRedirectServices;  
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Entity\EntityTypeBundleInfo;

/**
 * config form for content redirect to custom internal path.
 *
 * @internal
 */
class NodeRedirectConfigForm extends ConfigFormBase {  
  
  /**
   * The config .
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The path validator.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * The bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $entityBundleInfo;

  /**
   * Constructs a new ContactFormEditForm.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The email validator.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfo $entity_bundle_info
   *   The entity type bundle info service
   */
  public function __construct(ConfigFactory $config_factory, PathValidatorInterface $path_validator, EntityTypeBundleInfo $entity_bundle_info) {
    $this->configFactory = $config_factory;
    $this->pathValidator = $path_validator;
    $this->entityBundleInfo = $entity_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('path.validator'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'form_node_redirect.adminsettings',  
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
    
    $config = $this->config('form_node_redirect.adminsettings');  
    $node_types = $this->entityBundleInfo->getBundleInfo('node');

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
      $this->configFactory->getEditable('form_node_redirect.adminsettings')->set($key , $value)->save();
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
      $url_object = $this->pathValidator->getUrlIfValid($value);
      if ($key == 'submit') {
        break;
      }

      if (!empty($value)) {
        if (mb_substr($value, 0, 1) !== '/') {
          $form_state->setErrorByName($value , t(' The path should start with /.'));
        }
        if ($url_object == FALSE) {
          $form_state->setErrorByName($value , t(' The path ' . $value . ' is not a valid internal path.'));  
        }
      }
    }
  }  
}  
