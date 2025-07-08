<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../../fpdf186/fpdf.php';

class PretController
{
    public static function getAll()
    {
        $data = Pret::getAll();
        Flight::json($data);
    }

    public static function getById($id)
    {
        $data = Pret::getById($id);
        if ($data) {
            Flight::json($data);
        } else {
            Flight::halt(404, json_encode(['error' => 'Prêt non trouvé']));
        }
    }

    public static function create()
    {
        $data = Flight::request()->data;

        $id_client = $data->id_client;
        $montant_pret = (float) $data->montant;


        $solde_disponible = AjoutFonds::getSoldeClient($id_client);


        if ($montant_pret > $solde_disponible) {
            Flight::json([
                'error' => "Solde insuffisant. Solde disponible : {$solde_disponible} Ar, montant demandé : {$montant_pret} Ar"
            ], 400); // 400 = Bad Request
            return;
        }


        $id = Pret::create($data);
        Flight::json(['message' => 'Prêt créé', 'id' => $id]);
    }
    public static function update($id)
    {
        $data = Flight::request()->data;
        Pret::update($id, $data);
        Flight::json(['message' => 'Prêt mis à jour']);
    }

    public static function delete($id)
    {
        Pret::delete($id);
        Flight::json(['message' => 'Prêt supprimé']);
    }
    public static function creerpdf()
    {
        $data = Flight::request()->data->getData();
        $model = new Pret();

        try {
            $pretInfo = $model->calculerRemboursement(
                $data['id_client'],
                $data['id_type_pret'],
                $data['montant'],
                $data['mois']
            );

            if (ob_get_length()) {
                ob_end_clean();
            }


            $pdf = new FPDF();
            $pdf->AddPage();


            $pdf->SetFont('Arial', 'B', 18);
            $pdf->SetTextColor(0, 102, 204);
            $pdf->Cell(0, 10, "FICHE DE PRET", 0, 1, 'C');
            $pdf->Ln(3);


            $pdf->SetDrawColor(0, 102, 204);
            $pdf->SetLineWidth(0.8);
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
            $pdf->Ln(10);


            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor(0);
            $client_model = new Client();
            $nom_client = $client_model->getById($data['id_client'])['nom_client'] ?? 'Inconnu';
            $taux_model = new TauxPret();

            $nom_type_pret = $taux_model->getTypePretByIdTauxPret($data['id_type_pret'])['nom_type_pret'] ?? 'Inconnu';
            $infos = [
                "Nom du client"    => $nom_client,
                "Type de pret"     => $nom_type_pret,
                "Montant"          => number_format($data['montant'], 2, '.', ' ') . " Ar",
                "Duree"            => $data['mois'] . " mois",
                "Taux applique"    => $pretInfo['taux'] . " %"
            ];

            foreach ($infos as $label => $value) {
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetTextColor(50, 50, 50);
                $pdf->Cell(60, 10, $label . " :", 0, 0);
                $pdf->SetFont('Arial', '', 12);
                $pdf->SetTextColor(0);
                $pdf->Cell(0, 10, $value, 0, 1);
            }

            $pdf->Ln(15);
            $pdf->SetFont('Arial', '', 13);
            $pdf->SetFillColor(230, 250, 255);
            $pdf->Cell(0, 12, "Remboursement Mensuel : " . number_format($pretInfo['mensualite'], 2, '.', ' ') . " Ar / mois", 1, 1, 'C', true);


            $pdf->Ln(15);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->Cell(0, 10, date("d/m/Y H:i"), 0, 0, 'C');


            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="pret.pdf"');
            $pdf->Output('I', 'pret.pdf');
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
            exit;
        }
    }
}
