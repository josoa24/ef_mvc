<?php

class Utils {
    public static function formatDate($date) {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }

    public static function calculerAnnuite($montant, $taux_annuel, $duree_mois) {
        $taux_mensuel = $taux_annuel / 12;

        if ($taux_mensuel == 0) {
            return $montant / $duree_mois;
        }

        $annuite = $montant * ($taux_mensuel / (1 - pow(1 + $taux_mensuel, -$duree_mois)));
        return round($annuite, 2);
    }
}