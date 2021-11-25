<?php

use Phinx\Migration\AbstractMigration;

class Request extends AbstractMigration {
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
        $table = $this->table('requests');
        $table->addColumn('CodecList', 'string');
        $table->addColumn('SourceList', 'string');
        $table->addColumn('ContainerList', 'string');
        $table->addColumn('ResolutionList', 'string');
        $table->addColumn('IMDBID', 'string');
        $table->addColumn('Subtitle', 'string');
        $table->addColumn('SourceTorrent', 'string');
        $table->addColumn('PurchasableAt', 'string');
        $table->save();
    }
}
