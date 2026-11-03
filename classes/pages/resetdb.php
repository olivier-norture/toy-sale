<?php
namespace classes\pages;

use classes\pages\Page;

use PDO;
use classes\db\DB;
use classes\db\object\User;

class ResetDB extends Page{

    /**
     * @var bool true once the reset has been done successfully
     */
    private $resetDone = false;

    /**
     * @inheritDoc
     */
    protected function pageProcess() {
        if (!isset($_POST["action"]) || $_POST["action"] != "reset") {
            return;
        }

        $user = $this->getUser();
        if (!$user->getIsAdmin()) {
            $this->setErrorMessage("Seul un administrateur peut réinitialiser la base.");
            return;
        }

        // Ask the current user to retype his password to confirm the reset
        $password = isset($_POST["password"]) ? $_POST["password"] : "";
        $checked = User::searchForConnect($user->getLogin(), $password);
        if ($password === "" || $checked->getPk() == null || $checked->getPk() != $user->getPk()) {
            $this->setErrorMessage("Mot de passe incorrect, la base n'a pas été réinitialisée.");
            return;
        }

        $this->reset();
        $this->resetDone = true;
    }

    public function isResetDone() {
        return $this->resetDone;
    }

    public function reset() {
        $conn = DB::getConn();
        $conn->beginTransaction();
        try {
            $conn->prepare("delete from bill_objects")->execute();
            $conn->prepare("delete from bill")->execute();
            $conn->prepare("delete from objet")->execute();
            $conn->prepare("update pc set counter = 0")->execute();
            $conn->prepare("update participant set REF = NULL")->execute();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
?>
