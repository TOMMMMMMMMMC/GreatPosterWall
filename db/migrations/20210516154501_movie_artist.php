<?php

use Phinx\Migration\AbstractMigration;

class MovieArtist extends AbstractMigration {
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
        $t->addColumn('DoubanID', 'integer');
        $t->addColumn('DoubanVote', 'integer');
        $t->addColumn('IMDBVote', 'integer');
        $t->save();
        $wt = $this->table('wiki_torrents');
        $wt->addColumn('DoubanID', 'integer');
        $wt->addColumn('DoubanVote', 'integer');
        $wt->addColumn('IMDBVote', 'integer');
        $wt->save();
        $c = $this->table('movie_info_cache');
        $c->addColumn('DoubanID', 'integer');
        $c->addColumn('TMDBID', 'integer');
        $c->save();
        $a = $this->table('wiki_artists');
        $a->addColumn('Birthday', 'string');
        $a->addColumn('PlaceOfBirth', 'string');
        $a->save();
    }
}
