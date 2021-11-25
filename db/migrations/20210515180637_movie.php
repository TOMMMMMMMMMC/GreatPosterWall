<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class Movie extends AbstractMigration {
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
        $table = $this->table('movie_info_cache');
        $table->addColumn("TMDBData", 'text', array('limit' => MysqlAdapter::TEXT_LONG));
        $table->addColumn("IMDBActorData", 'text', array('limit' => MysqlAdapter::TEXT_LONG));
        $table->addColumn("DoubanActorData", 'text', array('limit' => MysqlAdapter::TEXT_LONG));
        $table->addColumn("DoubanData", 'text', array('limit' => MysqlAdapter::TEXT_LONG));
        $table->addColumn("DoubanTime", 'datetime');
        $table->addColumn("DoubanActorTime", 'datetime');
        $table->addColumn("IMDBActorTime", 'datetime');
        $table->addColumn("TMDBTime", 'datetime');
        $table->addColumn("OMDBTime", 'datetime');
        $table->save();
    }
}
