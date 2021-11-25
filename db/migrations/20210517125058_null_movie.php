<?php

use Phinx\Migration\AbstractMigration;

class NullMovie extends AbstractMigration {
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
        $t = $this->table('torrents_group');
        $t->changeColumn('RTRating', 'string', ['null' => true, 'default' => NULL]);
        $t->changeColumn('DoubanID', 'integer', ['null' => true, 'default' => NULL]);
        $t->changeColumn('DoubanVote', 'integer', ['null' => true, 'default' => NULL]);
        $t->changeColumn('IMDBVote', 'integer', ['null' => true, 'default' => NULL]);
        $t->changeColumn('RTTitle', 'string', ['null' => true, 'default' => NULL]);
        $t->save();

        $t1 = $this->table('torrents_group');
        $t1->changeColumn('RTRating', 'string', ['null' => true, 'default' => NULL]);
        $t1->changeColumn('DoubanID', 'integer', ['null' => true, 'default' => NULL]);
        $t1->changeColumn('DoubanVote', 'integer', ['null' => true, 'default' => NULL]);
        $t1->changeColumn('IMDBVote', 'integer', ['null' => true, 'default' => NULL]);
        $t1->changeColumn('RTTitle', 'string', ['null' => true, 'default' => NULL]);
        $t1->save();
    }
}
