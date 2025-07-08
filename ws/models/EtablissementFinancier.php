<?php
require_once __DIR__ . '/../db.php';

class EtablissementFinancier
{
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM ef_pret_db_etablissement_financier");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_pret_db_etablissement_financier WHERE id_etablissement_financier = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO ef_pret_db_etablissement_financier (nom_etablissement_financier, fonds_de_base) VALUES (?, ?)");
        $stmt->execute([$data->nom_etablissement_financier, $data->fonds_de_base]);
        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE ef_pret_db_etablissement_financier SET nom_etablissement_financier = ?, fonds_de_base = ? WHERE id_etablissement_financier = ?");
        $stmt->execute([$data->nom_etablissement_financier, $data->fonds_de_base, $id]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM ef_pret_db_etablissement_financier WHERE id_etablissement_financier = ?");
        $stmt->execute([$id]);
    }


    public static function getMontantTotalParMoisAvecPretCalcule($dateDebut, $dateFin)
    {
        $db = getDB();

        $sqlFonds = "
        SELECT 
            ef.id_etablissement_financier,
            ef.nom_etablissement_financier,
            ef.fonds_de_base,
            COALESCE(SUM(af.montant), 0) AS total_ajout_fonds
        FROM ef_pret_db_etablissement_financier ef
        LEFT JOIN ef_pret_db_ajout_fonds af 
            ON ef.id_etablissement_financier = af.id_etablissement_financier
            AND af.date_ajout BETWEEN :dateDebut AND :dateFin
        GROUP BY ef.id_etablissement_financier
    ";

        $stmt = $db->prepare($sqlFonds);
        $stmt->execute([
            ':dateDebut' => $dateDebut,
            ':dateFin' => $dateFin
        ]);

        $fondsParEF = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fondsParEF[$row['id_etablissement_financier']] = [
                'nom' => $row['nom_etablissement_financier'],
                'fonds_de_base' => $row['fonds_de_base'],
                'total_ajout_fonds' => $row['total_ajout_fonds'],
                'remboursements' => 0,
                'nouveaux_prets' => 0,
            ];
        }


        $sqlPrets = "
        SELECT p.*, t.taux, t.min_mois, t.max_mois, tp.id_type_pret, tp.nom_type_pret
        FROM ef_pret_db_pret p
        JOIN ef_pret_db_taux_pret t ON p.id_taux_pret = t.id_taux_pret
        JOIN ef_pret_db_type_pret tp ON t.id_type_pret = tp.id_type_pret
        WHERE p.date_pret <= :dateFin
    ";

        $stmt = $db->prepare($sqlPrets);
        $stmt->execute([':dateFin' => $dateFin]);
        $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $periodStart = new DateTime($dateDebut);
        $periodEnd = new DateTime($dateFin);


        $montantsParMois = [];

        foreach ($prets as $pret) {
            $idEF = null;

            $montant = floatval($pret['montant']);
            $tauxAnnuel = floatval($pret['taux']);
            $dureeMois = intval($pret['duree_mois']);
            $datePret = new DateTime($pret['date_pret']);

            $interetsTotal = $montant * $tauxAnnuel / 100 * ($dureeMois / 12);
            $montantTotal = $montant + $interetsTotal;
            $mensualite = $montantTotal / $dureeMois;

            for ($i = 0; $i < $dureeMois; $i++) {
                $moisPaiement = clone $datePret;
                $moisPaiement->modify("+$i month");
                if ($moisPaiement < $periodStart || $moisPaiement > $periodEnd) {
                    continue;
                }
                $moisKey = $moisPaiement->format('Y-m');

                if (!isset($montantsParMois[$moisKey])) {
                    $montantsParMois[$moisKey] = 0;
                }
                $montantsParMois[$moisKey] += $mensualite;
            }
        }


        $result = [];
        $moisCourant = clone $periodStart;
        while ($moisCourant <= $periodEnd) {
            $moisKey = $moisCourant->format('Y-m');
            $remboursements = isset($montantsParMois[$moisKey]) ? $montantsParMois[$moisKey] : 0;


            $totalPretsMois = 0;
            foreach ($prets as $pret) {
                $datePret = new DateTime($pret['date_pret']);
                if ($datePret->format('Y-m') === $moisKey) {
                    $totalPretsMois += floatval($pret['montant']);
                }
            }

            $fonds = 0;
            foreach ($fondsParEF as $ef) {
                $fonds += $ef['fonds_de_base'] + $ef['total_ajout_fonds'];
            }

            $montantDisponible = $fonds + $remboursements - $totalPretsMois;

            $result[] = [
                'mois' => $moisKey,
                'montant_disponible' => round($montantDisponible, 2),
                'remboursements' => round($remboursements, 2),
                'prets_engages' => round($totalPretsMois, 2),
            ];

            $moisCourant->modify('+1 month');
        }

        return $result;
    }

    public static function calculMontantsDisponiblesParMois(string $dateDebut, string $dateFin)
    {
        $db = getDB();
        $sqlFonds = "
            SELECT DATE_FORMAT(date_ajout, '%Y-%m') AS mois, SUM(montant) AS total_fonds
            FROM ef_pret_db_ajout_fonds
            WHERE date_ajout BETWEEN :dateDebut AND :dateFin
            GROUP BY mois
            ORDER BY mois
        ";
        $stmt = $db->prepare($sqlFonds);
        $stmt->execute(['dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
        $fonds = $stmt->fetchAll(PDO::FETCH_ASSOC);

      
        $sqlRemb = "
            SELECT DATE_FORMAT(date_paiement, '%Y-%m') AS mois, SUM(montant_rembourse) AS total_rembourse
            FROM ef_pret_db_remboursement
            WHERE date_paiement BETWEEN :dateDebut AND :dateFin
            GROUP BY mois
            ORDER BY mois
        ";
        $stmt = $db->prepare($sqlRemb);
        $stmt->execute(['dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
        $remboursements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlPrets = "
            SELECT DATE_FORMAT(date_pret, '%Y-%m') AS mois, SUM(montant) AS total_prets
            FROM ef_pret_db_pret
            WHERE date_pret BETWEEN :dateDebut AND :dateFin
            GROUP BY mois
            ORDER BY mois
        ";
        $stmt = $db->prepare($sqlPrets);
        $stmt->execute(['dateDebut' => $dateDebut, 'dateFin' => $dateFin]);
        $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $moisList = [];
        foreach ([$fonds, $remboursements, $prets] as $list) {
            foreach ($list as $item) {
                $moisList[$item['mois']] = true;
            }
        }
        $moisList = array_keys($moisList);
        sort($moisList);

        $result = [];
        foreach ($moisList as $mois) {
            $totalFonds = 0;
            $totalRemb = 0;
            $totalPrets = 0;

            foreach ($fonds as $f) {
                if ($f['mois'] === $mois) {
                    $totalFonds = (float)$f['total_fonds'];
                    break;
                }
            }
            foreach ($remboursements as $r) {
                if ($r['mois'] === $mois) {
                    $totalRemb = (float)$r['total_rembourse'];
                    break;
                }
            }
            foreach ($prets as $p) {
                if ($p['mois'] === $mois) {
                    $totalPrets = (float)$p['total_prets'];
                    break;
                }
            }

            $montantDisponible = $totalFonds + $totalRemb - $totalPrets;

            $result[] = [
                'mois' => $mois,
                'montant_disponible' => $montantDisponible,
                'remboursements' => $totalRemb,
                'prets_engages' => $totalPrets,
            ];
        }

        return $result;
    }
}
