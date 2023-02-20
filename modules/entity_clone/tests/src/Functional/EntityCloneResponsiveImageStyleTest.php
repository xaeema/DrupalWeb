<?php

namespace Drupal\Tests\entity_clone\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;
use Drupal\Tests\BrowserTestBase;

/**
 * Create a responsive image style and test a clone.
 *
 * @group entity_clone
 */
class EntityCloneResponsiveImageStyleTest extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['entity_clone', 'responsive_image'];

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
    'clone responsive_image_style entity',
    'administer responsive images',
  ];

  /**
   * An administrative user with permission to configure image styles settings.
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
   * Test responsive image style entity clone.
   */
  public function testResponsiveImageStyleEntityClone() {
    $edit = [
      'label' => 'Test responsive image style for clone',
      'id' => 'test_responsive_image_style_for_clone',
      'breakpoint_group' => 'responsive_image',
      'fallback_image_style' => 'large',
    ];
    $this->drupalGet("admin/config/media/responsive-image-style/add");
    $this->submitForm($edit, $this->t('Save'));

    $responsive_image_styles = \Drupal::entityTypeManager()
      ->getStorage('responsive_image_style')
      ->loadByProperties([
        'id' => $edit['id'],
      ]);
    $responsive_image_style = reset($responsive_image_styles);

    $edit = [
      'id' => 'test_responsive_image_style_cloned',
      'label' => 'Test responsive image style cloned',
    ];
    $this->drupalGet('entity_clone/responsive_image_style/' . $responsive_image_style->id());
    $this->submitForm($edit, $this->t('Clone'));

    $responsive_image_styles = \Drupal::entityTypeManager()
      ->getStorage('responsive_image_style')
      ->loadByProperties([
        'id' => $edit['id'],
      ]);
    $responsive_image_style = reset($responsive_image_styles);
    $this->assertInstanceOf(ResponsiveImageStyle::class, $responsive_image_style, 'Test responsive image style cloned found in database.');
  }

}
