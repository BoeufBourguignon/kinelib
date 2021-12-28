<?php

namespace App\Service;

use App\Repository\EDTRepository;
use App\Repository\RDVRepository;
use App\Repository\UserRepository;

class EdtManagerService
{
    private EDTRepository $EDTRepository;
    private RDVRepository $RDVRepository;
    private UserRepository $userRepository;
    private array $jours = array(
        '0' => 'Lundi',
        '1' => 'Mardi',
        '2' => 'Mercredi',
        '3' => 'Jeudi',
        '4' => 'Vendredi',
        '5' => 'Samedi',
        '6' => 'Dimanche'
    );

    public function __construct(
          EDTRepository $EDTRepository
        , RDVRepository $RDVRepository
        , UserRepository $userRepository
    ) {
        $this->EDTRepository = $EDTRepository;
        $this->RDVRepository = $RDVRepository;
        $this->userRepository = $userRepository;
        date_default_timezone_set('Europe/Paris');
    }

    public function getEdtKineForOneWeek(string $idKine, int $startTimestamp = 0): array {
        if ($startTimestamp == 0)
            $startTimestamp = time();

        $kine = $this->userRepository->find($idKine);

        $now = date('H:i', $startTimestamp);
        $today = strtotime('today');

        $currDay = $startTimestamp;
        $edt = array();
        for ($j=0;$j<7;$j++) {
            // 0: Lundi, 1: Mardi, 2: Mercredi, ...
            $currDayNumber = date('N', $currDay) - 1 != -1 ? date('N', $currDay) - 1 : 6;

            //EDT du kiné pour le jour courant (all ou aprem et matin)
            $edtKine = $this->EDTRepository
                ->findBy([
                    'kine' => $kine,
                    'jour' => $currDayNumber
                ]);
            //RDVs du kiné pour le jour courant
            $rdvs = $this->RDVRepository
                ->findBy([
                    'kine' => $kine,
                    'date' => (new \DateTime)->setTimestamp($currDay)
                ]);

            $edt[date('d/m/Y', $currDay)]['jour'] = $this->jours[$currDayNumber];
            if($currDay >= $today) {
                foreach ($edtKine as $edtKinePeriode) {
                    $heureDebut = date('H:i', strtotime($edtKinePeriode->getHeureDebut()));
                    $heureFin = date('H:i', strtotime($edtKinePeriode->getHeureFin()));
                    while ($heureDebut < $heureFin) {
                        if ($currDay != $startTimestamp || $heureDebut > $now) {
                            $isThereARdv = false;
                            if (count($rdvs)) {
                                $k = 0;
                                while ($k < (count($rdvs) - 1) && date('H:i', strtotime($rdvs[$k]->getHeureDebut())) != $heureDebut) {
                                    $k++;
                                }
                                if (date('H:i', strtotime($rdvs[$k]->getHeureDebut())) == $heureDebut) {
                                    $isThereARdv = true;
                                }
                            }
                            if (!$isThereARdv) {
                                $edt[date('d/m/Y', $currDay)]['heures'][] = $heureDebut;
                            }
                        }
                        $heureDebut = date('H:i', strtotime('+1 hour', strtotime($heureDebut)));
                    }
                    //$edt[date('d/m/Y', $currDay)][$edtKinePeriode->getPeriode()]
                }
            }

            $currDay = strtotime('+1 day', $currDay);
        }

        return $edt;
    }

    public function getRdvKineForOneWeek(string $idKine, int $startDate = 0): array
    {
        if($startDate == 0)
            $startDate = strtotime('Monday this week');

        $kine = $this->userRepository->find($idKine);

        $currDay = $startDate;
        $rdvs = array();
        for ($j=0;$j<7;$j++) {
            // 0: Lundi, 1: Mardi, 2: Mercredi, ...
            $currDayNumber = date('N', $currDay) - 1 != -1 ? date('N', $currDay) - 1 : 6;

            //RDVs du kiné pour le jour courant
            $rdvs = $this->RDVRepository
                ->findBy([
                    'kine' => $kine,
                    'date' => (new \DateTime)->setTimestamp($currDay)
                ]);

            $rdvsTmp[date('d/m/Y', $currDay)]['jour'] = $this->jours[$currDayNumber];
            foreach ($rdvs as $rdv) {
                $rdvsTmp[date('d/m/Y', $currDay)]['rdvs'][$rdv->getHeureDebut()]['nom'] = $rdv->getUser()->getNom();
                $rdvsTmp[date('d/m/Y', $currDay)]['rdvs'][$rdv->getHeureDebut()]['prenom'] = $rdv->getUser()->getPrenom();
            }

            if(isset($rdvsTmp[date('d/m/Y', $currDay)]['rdvs']))
                ksort($rdvsTmp[date('d/m/Y', $currDay)]['rdvs']);
            $currDay = strtotime('+1 day', $currDay);
        }

        return $rdvsTmp;
    }
}