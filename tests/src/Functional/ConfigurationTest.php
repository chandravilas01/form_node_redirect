<?php

namespace Drupal\Tests\form_node_redirect\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test form the module configurations.
 *
 * @group form_node_redirect
 */
class ConfigurationTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['form_node_redirect'];

  /**
   * Tests the configuration form, the permission and the link.
   */
  public function testConfigurationForm() {
    // Going to the config page.
    $this->drupalGet('/admin/config/form_node_redirect/adminsettings');

    // Checking that the page is not accesible for anonymous users.
    $this->assertSession()->statusCodeEquals(403);

    // Creating a user with the module permission.
    $account = $this->drupalCreateUser(['administer form_node_redirect', 'access administration pages']);
    // Log in.
    $this->drupalLogin($account);

    // Checking the module link.
    $this->drupalGet('/admin/config/system');
    $this->assertSession()->linkByHrefExists('/admin/config/form_node_redirect/adminsettings');

    // Going to the config page.
    $this->drupalGet('/admin/config/form_node_redirect/adminsettings');
    // Checking that the request has succeeded.
    $this->assertSession()->statusCodeEquals(200);
    // Checking the page title.

    $this->assertSession()->elementTextContains('css', 'h1', 'Redirect Node Form');

    $this->assertSession()->elementTextContains('css', 'label', 'Enter redirect paths for following content types');  
  }
}
