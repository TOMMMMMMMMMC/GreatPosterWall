<?php

use Phinx\Migration\AbstractMigration;

class Sphinx extends AbstractMigration {
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
        $tg = $this->table('sphinx_tg');
        $tg->addColumn('rtrating', 'string');
        $tg->save();
        $t = $this->table('sphinx_t');
        $t->addColumn('processing', 'string');
        $t->save();
        $d = $this->table('sphinx_delta');
        $d->addColumn('RTRating', 'string');
        $d->addColumn('Processing', 'string');
        $d->save();

        $r = $this->table('sphinx_requests');
        $r->addColumn('CodecList', 'string');
        $r->addColumn('SourceList', 'string');
        $r->addColumn('ContainerList', 'string');
        $r->addColumn('ResolutionList', 'string');
        $r->save();

        $rd = $this->table('sphinx_requests_delta');
        $rd->addColumn('CodecList', 'string');
        $rd->addColumn('SourceList', 'string');
        $rd->addColumn('ContainerList', 'string');
        $rd->addColumn('ResolutionList', 'string');
        $rd->save();
    }
}
