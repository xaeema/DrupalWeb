<?php

namespace Drupal\Tests\entity_clone\Functional;

use Drupal\contact\Entity\ContactForm;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Create an contact form and test a clone.
 *
 * @group entity_clone
 */
class EntityCloneContactTest extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['entity_clone', 'contact'];

  /**
   * Theme to enable by default.
   *
   * @var string
   */
  protected $defaultTheme = 'claro';

  /**
   * Permissions to grant admin user.
   *
   * @var array
   */
  protected $permissions = [
    'administer contact forms',
    'clone contact_form entity',
  ];

  /**
   * An administrative user with permission to configure contact forms settings.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Sets the test up.
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser($this->permissions);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test contact form entity clone.
   */
  public function testContactFormsEntityClone() {

    $edit = [
      'label' => 'Test contact form for clone',
      'id' => 'test_contact_form_for_clone',
      'recipients' => 'test@recipient.com',
    ];
    $this->drupalGet('admin/structure/contact/add');
    $this->submitForm($edit, $this->t('Save'));

    $contact_forms = \Drupal::entityTypeManager()
      ->getStorage('contact_form')
      ->loadByProperties([
        'id' => $edit['id'],
      ]);
    $contact_form = reset($contact_forms);

    $edit = [
      'label' => 'Test contact form cloned',
      'id' => 'test_contact_form_cloned',
    ];
    $this->drupalGet('entity_clone/contact_form/' . $contact_form->id());
    $this->submitForm($edit, $this->t('Clone'));

    $contact_forms = \Drupal::entityTypeManager()
      ->getStorage('contact_form')
      ->loadByProperties([
        'id' => $edit['id'],
      ]);
    $contact_form = reset($contact_forms);
    $this->assertInstanceOf(ContactForm::class, $contact_form, 'Test contact form cloned found in database.');

    $edit = [
      'id' => 'test_contact_form_clone_with_a_really_long_name_that_is_longer_than_the_bundle_max_length',
      'label' => 'Test contact form clone with a really long name that is longer than the bundle max length',
    ];
    $this->drupalGet('entity_clone/contact_form/' . $contact_form->id());
    $this->submitForm($edit, $this->t('Clone'));

    $this->assertSession()->pageTextContains('New Id cannot be longer than 32 characters');
  }

}
