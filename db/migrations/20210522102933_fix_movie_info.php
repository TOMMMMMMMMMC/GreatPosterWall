<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixMovieInfo extends AbstractMigration {
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
        $t = $this->table('movie_info_cache');
        $t->changeColumn('TMDBID', 'integer', ['null' => true, 'default' => NULL]);
        $t->changeColumn('DoubanID', 'integer', ['null' => true, 'default' => NULL]);
        $t->changeColumn('DoubanData', 'text', ['null' => true, 'default' => NULL, 'limit' => MysqlAdapter::TEXT_LONG]);
        $t->changeColumn('DoubanActorData', 'text', ['null' => true, 'default' => NULL, 'limit' => MysqlAdapter::TEXT_LONG]);
        $t->changeColumn('IMDBActorData', 'text', ['null' => true, 'default' => NULL, 'limit' => MysqlAdapter::TEXT_LONG]);
        $t->changeColumn('TMDBData', 'text', ['null' => true, 'default' => NULL, 'limit' => MysqlAdapter::TEXT_LONG]);
        $t->save();

        $t2 = $this->table('artist_info_cache');
        $t2->changeColumn('TMDBData', 'text', ['null' => true, 'default' => NULL, 'limit' => MysqlAdapter::TEXT_LONG]);
        $t2->changeColumn('TMDBID', 'integer', ['null' => true, 'default' => NULL]);
        $t2->save();
    }
}
