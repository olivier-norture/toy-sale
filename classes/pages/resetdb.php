<?php
namespace classes\pages;

use classes\pages\Page;

use PDO;
use classes\db\DB;

class ResetDB extends Page{
    /**
     * @inheritDoc
     */
    protected function pageProcess() {
        parent::pageProcess();
    }

    public function reset() {
        DB::getConn()->prepare("delete from bill_objects")->execute();
        DB::getConn()->prepare("delete from bill")->execute();
        DB::getConn()->prepare("delete from objet")->execute();
        DB::getConn()->prepare("update pc set counter = 0")->execute();
        DB::getConn()->prepare("update participant set REF = NULL")->execute();
    }
}
?>