<?php

require_once __DIR__ . '/../models/TauxPret.php';

class TauxPretController {

    public static function ajouter() {
        $data = Flight::request()->data->getData();

        $type_pret = $data['type_pret'];
        $inf_mois = $data['inf_mois'];
        $inf_taux = $data['inf_taux'];
        $sup_mois = $data['sup_mois'];
        $sup_taux = $data['sup_taux'];
        $min_list = $data['min'];
        $max_list = $data['max'];
        $taux_list = $data['taux'];

        $model = new TauxPret();

        try {
            $model->ajouter_pret_et_taux(
                $type_pret,
                $inf_mois, $inf_taux,
                $min_list, $max_list, $taux_list,
                $sup_mois, $sup_taux
            );
            Flight::json(["success" => true, "message" => "Taux enregistrés avec succès"]);
        } catch (Exception $e) {
            Flight::json(["success" => false, "message" => $e->getMessage()]);
        }
    }
}
