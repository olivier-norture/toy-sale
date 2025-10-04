<?php
namespace classes\pages;

use classes\pages\Page;
use classes\db\object\Participant;

class RestitutionEnvelope extends Page{
    /**
     * @inheritDoc
     */
    protected function pageProcess() {
        parent::pageProcess();
    }

    public function envelopeGetAllParticipants(){
        $res = array();
        foreach(Participant::getSellers() as $seller ){
            $res [] = $seller->getRef() . " - " . $seller->getNom() ." " . $seller->getPnom();
        }
        return $res;
    }
}
?>