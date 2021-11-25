<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class ArtistCache extends AbstractMigration {
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
        $table = $this->table('artist_info_cache', array('id' => false, 'primary_key' => array('IMDBID')));
        $table->addColumn('IMDBID', 'string');
        $table->addColumn('TMDBID', 'integer');
        $table->addColumn("TMDBData", 'text', array('limit' => MysqlAdapter::TEXT_LONG));
        $table->addColumn("TMDBTime", 'datetime');
        $table->save();
    }
}
