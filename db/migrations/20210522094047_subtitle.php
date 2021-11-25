<?php

use Phinx\Migration\AbstractMigration;

class Subtitle extends AbstractMigration {
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change() {
        $table = $this->table('subtitles_files');
        $table->addColumn('File', 'blob');
        $table->save();

        $table2 = $this->table('subtitles');
        $table2->addColumn('languages', 'string');
        $table2->addColumn('torrent_id', 'integer');
        $table2->addColumn('source', 'string');
        $table2->addColumn('download_times', 'string');
        $table2->addColumn('format', 'string');
        $table2->addColumn('size', 'integer');
        $table2->addColumn('uploader', 'integer');
        $table2->addColumn('upload_time', 'datetime');
        $table2->addColumn('name', 'string');
        $table2->save();
    }
}
