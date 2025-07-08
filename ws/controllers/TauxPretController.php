<?php

require_once __DIR__ . '/../models/TauxPret.php';

class TauxPretController
{
    public static function ajouter()
    {
        $data = Flight::request()->data->getData();

        $type_pret = $data['type_pret'];
        $inf_mois = intval($data['inf_mois']);
        $inf_taux = floatval($data['inf_taux']);
        $sup_mois = intval($data['sup_mois']);
        $sup_taux = floatval($data['sup_taux']);
        $min_list = array_map('intval', $data['min']);
        $max_list = array_map('intval', $data['max']);
        $taux_list = array_map('floatval', $data['taux']);

        try {
            // ✅ Étape 1 : Créer une liste de tous les mois à couvrir
            $mois_total = range(0, $sup_mois);
            $mois_couverts = [];

            // ✅ Étape 2 : Ajouter les mois couverts par l'intervalle inférieur
            for ($i = 0; $i <= $inf_mois; $i++) {
                $mois_couverts[$i] = true;
            }

            // ✅ Étape 3 : Ajouter les mois couverts par les intervalles du milieu
            for ($i = 0; $i < count($min_list); $i++) {
                for ($m = $min_list[$i]; $m <= $max_list[$i]; $m++) {
                    $mois_couverts[$m] = true;
                }
            }

            // ✅ Étape 4 : Ajouter les mois couverts par le sup
            for ($i = $sup_mois + 1; $i <= 1000; $i++) {
                $mois_couverts[$i] = true;
            }

            // ✅ Étape 5 : Vérifier s’il y a un mois manquant de 0 à sup_mois
            $mois_oublies = [];
            foreach ($mois_total as $m) {
                if (!isset($mois_couverts[$m])) {
                    $mois_oublies[] = $m;
                }
            }

            if (!empty($mois_oublies)) {
                throw new Exception("Les mois suivants ne sont couverts par aucun intervalle : " . implode(', ', $mois_oublies));
            }

            // ✅ Étape : créer la liste de tous les intervalles
            $intervalles = [];
            $intervalles[] = ['min' => 0, 'max' => $inf_mois];
            for ($i = 0; $i < count($min_list); $i++) {
                $intervalles[] = ['min' => $min_list[$i], 'max' => $max_list[$i]];
            }
            $intervalles[] = ['min' => $sup_mois + 1, 'max' => 1000];

            // ✅ Étape : trier les intervalles par min croissant
            usort($intervalles, function ($a, $b) {
                return $a['min'] - $b['min'];
            });

            // ✅ Étape : vérifier les chevauchements
            for ($i = 1; $i < count($intervalles); $i++) {
                $prev = $intervalles[$i - 1];
                $curr = $intervalles[$i];

                if ($curr['min'] <= $prev['max']) {
                    throw new Exception("Chevauchement détecté entre les intervalles [{$prev['min']}, {$prev['max']}] et [{$curr['min']}, {$curr['max']}]");
                }
            }

            // ✅ Insertion si tout est ok
            $model = new TauxPret();
            $model->ajouter_pret_et_taux(
                $type_pret,
                $inf_mois,
                $inf_taux,
                $min_list,
                $max_list,
                $taux_list,
                $sup_mois,
                $sup_taux
            );

            Flight::json(["success" => true, "message" => "Taux enregistrés avec succès"]);
        } catch (Exception $e) {
            Flight::json(["success" => false, "message" => $e->getMessage()]);
        }
    }

    public static function getAll() {
        $model = new TauxPret();
        $result = $model->getAllTaux();
        Flight::json($result);
    }
    
}
