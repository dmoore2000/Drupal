<?php

/**
 * @file
 * Contains \Drupal\imceimage\Plugin\migrate\builder\ImceImageField
 */

namespace Drupal\imceimage\Plugin\migrate\builder;

use Drupal\migrate\Entity\Migration;
use Drupal\migrate\Exception\RequirementsException;
use Drupal\migrate_drupal\Plugin\migrate\builder\CckBuilder;

/**
 * @PluginID("d6_imceimage_file")
 */
class ImceImageField extends CckBuilder {

    /**
     * {@inheritdoc}
     */
    public function buildMigrations(array $template) {
        $migrations = [];

        // Read all CCK field instance definitions in the source database.
        $fields = array();
        $source_plugin = $this->getSourcePlugin('d6_field_instance', $template['source']);
        try {
            $source_plugin->checkRequirements();

            foreach ($source_plugin as $field) {
                $info = $field->getSource();
                $fields[$info['type_name']][$info['field_name']] = $info;
            }
        }
        catch (RequirementsException $e) {
            // Don't do anything; $fields will be empty.
        }

        foreach ($this->getSourcePlugin('d6_node_type', $template['source']) as $row) {
            $node_type = $row->getSourceProperty('type');
            $values = $template;
            $values['id'] = $template['id'] . '__' . $node_type;

            $label = $template['label'];
            $values['label'] = $this->t("@label (@type)", ['@label' => $label, '@type' => $node_type]);
            $values['source']['node_type'] = $node_type;

            if (isset($fields[$node_type])) {
                foreach ($fields[$node_type] as $field => $info) {
                    if ($info['type']=='imceimage') {
                        // Create migration
                        $values = $template;
                        $values['id'] = $template['id'] . '__' . $node_type . '__' . $field;

                        $label = $template['label'];
                        $values['label'] = $this->t("@label (@type - @field)", ['@label' => $label, '@type' => $node_type, '@field' => $field]);
                        $values['source']['node_type'] = $node_type;
                        $values['source']['field_name'] = $field;

                        $migration = Migration::create($values);
                        $migrations[] = $migration;
                    }
                }
            }
        }

        return $migrations;
    }

}
