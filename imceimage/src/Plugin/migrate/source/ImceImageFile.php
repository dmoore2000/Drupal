<?php

/**
 * @file
 * Contains \Drupal\imceimage\Plugin\migrate\source\ImceImageFile.
 */

namespace Drupal\imceimage\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 6 file source from database.
 *
 * @MigrateSource(
 *   id = "d6_imceimage_file"
 * )
 */
class ImceImageFile extends DrupalSqlBase {

    /**
     * The file directory path.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The temporary file path.
     *
     * @var string
     */
    protected $tempFilePath;

    /**
     * Flag for private or public file storage.
     *
     * @var bool
     */
    protected $isPublic;

    /**
     * {@inheritdoc}
     */
    public function query() {
        $query = $this->select('node_revisions', 'nr')
            ->fields('n', array(
                'nid',
                'type',
            ))
            ->fields('nr', array(
                'vid',
                'title',
            ));
        $query->innerJoin('node', 'n', 'n.vid = nr.vid');

        if (isset($this->configuration['node_type'])) {
            $query->condition('type', $this->configuration['node_type']);
        }

        if (isset($this->configuration['field_name'])) {
            $field_table = 'content_' . $field['field_name'];
            //$node_table = 'content_type_' . $node->getSourceProperty('type');

            /** @var \Drupal\Core\Database\Schema $db */
            $db = $this->getDatabase()->schema();

            $field_name = $this->configuration['field_name'];
            $field_table = 'content_' . $field_name;
            if (!$db->tableExists($field_table)) {
                $node_type = $this->configuration['node_type'];
                $field_table = 'content_type_' . $node_type;
            }

            $query->innerJoin($field_table, 'f', 'n.vid = f.vid');
            $query->addField('f', $field_name.'_imceimage_path','path');
            $query->isNotNull($field_name.'_imceimage_path');
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeIterator() {
        $site_path = isset($this->configuration['site_path']) ? $this->configuration['site_path'] : 'sites/default';
        $this->filePath = $this->variableGet('file_directory_path', $site_path . '/files') . '/';
        $this->tempFilePath = $this->variableGet('file_directory_temp', '/tmp') . '/';

        // FILE_DOWNLOADS_PUBLIC == 1 and FILE_DOWNLOADS_PRIVATE == 2.
        $this->isPublic = $this->variableGet('file_downloads', 1) == 1;
        return parent::initializeIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRow(Row $row) {
        $path = $row->getSourceProperty('path');

        $row->setSourceProperty('file_directory_path', $this->filePath);
        $row->setSourceProperty('temp_directory_path', $this->tempFilePath);
        $row->setSourceProperty('is_public', $this->isPublic);

        $row->setSourceProperty('filename', basename($path));
        $row->setSourceProperty('filepath', substr($path,1,strlen($path)));

        $row->setSourceProperty('status', 1);

        return parent::prepareRow($row);
    }

    /**
     * {@inheritdoc}
     */
    public function fields() {
        return array(
            'fid' => $this->t('File ID'),
            'uid' => $this->t('The {users}.uid who added the file. If set to 0, this file was added by an anonymous user.'),
            'filename' => $this->t('File name'),
            'filepath' => $this->t('File path'),
            'filemime' => $this->t('File Mime Type'),
            'status' => $this->t('The published status of a file.'),
            'timestamp' => $this->t('The time that the file was added.'),
            'file_directory_path' => $this->t('The Drupal files path.'),
            'is_public' => $this->t('TRUE if the files directory is public otherwise FALSE.'),
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getIds() {
        $ids['path']['type'] = 'text';
        $ids['path']['alias'] = 'f';
        return $ids;
    }

}