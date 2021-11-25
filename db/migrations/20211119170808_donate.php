<?php

use Phinx\Migration\AbstractMigration;

class Donate extends AbstractMigration {
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
        $table = $this->table('donations_prepaid_card');
        $table->addColumn('user_id', 'integer');
        $table->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('card_num', 'string');
        $table->addColumn('card_secret', 'string');
        $table->addColumn('face_value', 'string');
        $table->addIndex(['card_num', 'card_secret'], ['unique' => true]);
        $table->save();
    }
}
