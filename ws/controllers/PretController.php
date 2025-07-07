<?php 
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../../fpdf186/fpdf.php';

class PretController {
    public static function creerpdf() {
        $data = Flight::request()->data->getData();
        $model = new Pret();

        try {
            $pretInfo = $model->calculerRemboursement(
                1, 
                1,
                $data['montant'],
                $data['mois']
            );

            if (ob_get_length()) {
                ob_end_clean();
            }

            // Initialisation du PDF
            $pdf = new FPDF();
            $pdf->AddPage();

            // En-tête
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->SetTextColor(0, 102, 204); // bleu
            $pdf->Cell(0, 10, "FICHE DE PRET", 0, 1, 'C');
            $pdf->Ln(3);

            // Ligne de séparation
            $pdf->SetDrawColor(0, 102, 204);
            $pdf->SetLineWidth(0.8);
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
            $pdf->Ln(10);

            // Données
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor(0); // reset color

            $infos = [
                "Nom du client"    => "Mirana",
                "Type de pret"     => "Pret etudiant",
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

            // Encadré mensualité
            $pdf->Ln(15);
            $pdf->SetFont('Arial', '', 13);
            $pdf->SetFillColor(230, 250, 255);
            $pdf->Cell(0, 12, "Remboursement Mensuel : ".number_format($pretInfo['mensualite'], 2, '.', ' ') . " Ar / mois", 1, 1, 'C', true);

            // Pied de page
            $pdf->Ln(15);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->Cell(0, 10, date("d/m/Y H:i"), 0, 0, 'C');

            // Sortie
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
