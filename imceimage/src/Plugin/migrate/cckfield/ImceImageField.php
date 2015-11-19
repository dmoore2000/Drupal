<?php

/**
 * @file
 * Contains \Drupal\custom_migrate\Plugin\migrate\cckfield\ImceImage
 */

namespace Drupal\imceimage\Plugin\migrate\cckfield;

use Drupal\migrate\Entity\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\cckfield\CckFieldPluginBase;

use Drupal\mylog\Logger;

/**
 * @MigrateCckField(
 *   id = "imceimage"
 * )
 */
 
class ImceImageField extends CckFieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function processField(MigrationInterface $migration) {
    // Add Imceimage to process section of d6_field.yml migration template
    $process[0]['map'][$this->pluginId][$this->pluginId] = 'image';
    $migration->mergeProcessOfProperty('type', $process);
	\Drupal::logger('ImceImageField')->notice("<pre>migration=%s</pre>",array('%s'=>print_r($migration,true)));
  }

  /**
   * {@inheritdoc}
   */
  public function processFieldWidget(MigrationInterface $migration) {
    // Add ImceImage to options/type section of d6_field_instance_widget_settings.yml migration template
    $process['type']['map'][$this->pluginId] = 'image_image';
    $migration->mergeProcessOfProperty('options/type', $process);
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldFormatterMap() {
    return [
      'imceimage' => 'image',
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFieldType(Row $row) {
    return $row->getSourceProperty('widget_type') == 'image';
  }

  /**
   * {@inheritdoc}
   */
  public function processCckFieldValues(MigrationInterface $migration, $field_name, $data) {
      // Add our custom processing for ImceImage.
      /*
      $process = [
        'plugin' => 'ImceImage',
        'source' => [
          $field_name,
          $field_name . '_title',
          $field_name . '_attributes',
        ],
      ];
      $migration->mergeProcessOfProperty($field_name, $process);
	   
	   */
  }

}