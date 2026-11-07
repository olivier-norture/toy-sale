<?php
namespace classes\pages;

use classes\pages\Page;
use classes\db\object\Participant;
use classes\db\object\Objet;
use classes\db\object\Bill;
use classes\config\Constants;

/**
 * Read-only page which gathers, for every participant owning at least one object,
 * the restitution summary (sold, unsold, balance) in order to print them all at once.
 * Nothing is saved: no bill is created.
 */
class RestitutionAll extends Page {

    protected function pageProcess() {
        // Nothing to do, this page only reads data
    }

    /**
     * @return array[] One entry per participant : participant, selled, unselled, tax, totals
     */
    public function getSummaries() {
        $summaries = array();
        $seen = array();
        $defaultTax = (new Bill(null, null, 0, 0, 0))->getTax();

        foreach (Participant::getSellers() as $participant) {
            //A participant can appear once per letter, only print them once
            if (isset($seen[$participant->getId()])) {
                continue;
            }
            $seen[$participant->getId()] = true;

            $selled = array();
            $unselled = array();
            $totalSelled = 0;
            foreach (Objet::getAllFromVendor($participant->getId()) as $objet) {
                if ($objet->getPk_acheteur() > 0) {
                    $selled[] = $objet;
                    $totalSelled += $objet->getPrix();
                } else {
                    $unselled[] = $objet;
                }
            }

            //Use the commission of the last restitution if there is one
            $lastBill = Bill::searchLastActiveFromCustomer($participant->getId(), Constants::$BILL_TYPE_RESTITUTION);
            $tax = $lastBill != null ? $lastBill->getTax() : $defaultTax;

            $summaries[] = array(
                'participant' => $participant,
                'selled' => $selled,
                'unselled' => $unselled,
                'tax' => $tax,
                'totalSelled' => $totalSelled,
                'totalMinusTax' => $totalSelled * (1 - $tax / 100),
            );
        }
        return $summaries;
    }
}
