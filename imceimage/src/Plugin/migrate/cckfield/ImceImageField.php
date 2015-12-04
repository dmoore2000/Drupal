<?php

/**
 * @file
 * Contains \Drupal\custom_migrate\Plugin\migrate\cckfield\ImceImage
 */

namespace Drupal\imceimage\Plugin\migrate\cckfield;

use Drupal\migrate\Entity\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\cckfield\CckFieldPluginBase;


/**
 * @MigrateCckField(
 *   id = "imceimage"
 * )
 */
 
class ImceImageField extends CckFieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function processFieldWidget(MigrationInterface $migration) {
    // Add ImceImage to options/type section of d6_field_instance_widget_settings.yml migration template
    $process['type']['map']['imceimage'] = 'image_image';
    $migration->mergeProcessOfProperty('options/type', $process);
  }

  /**
   * {@inheritdoc}
   */

  public function getFieldFormatterMap() {
    return [
      'default' => 'image',
      'imceimage' => 'image',
    ];
  }
  
  /**
   * {@inheritdoc}
   */

  public function getFieldType(Row $row) {
    return 'image';
  }

  /**
   * {@inheritdoc}
   */
  public function processCckFieldValues(MigrationInterface $migration, $field_name, $data) {
      // Add our custom processing for ImceImage.
      $process = [
        'plugin' => 'd6_imceimage',
        'source' => $field_name,
      ];
      $migration->mergeProcessOfProperty($field_name, $process);

      // Add a dependency for the d6_imceimage_file migrations
      // Ideally this would be called via a new CckFieldPluginBase method called getDependencies
      // This would then be called by the d6_node.yml builder plugin to add dependencies to the migrations
      $dependencies = $migration->get('migration_dependencies');
      $dependencies['required'][] = 'd6_imceimage_file:*';
      $migration->set('migration_dependencies', $dependencies);
  }

}