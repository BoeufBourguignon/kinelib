<?php

namespace App\Controller;

use App\Entity\EDT;
use App\Repository\EDTRepository;
use App\Repository\RDVRepository;
use App\Repository\UserRepository;

use Symfony\Component\HttpFoundation\Request;
use App\Service\EdtManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * Récupère les disponibilités du kiné $idKine pour les 7 prochaines jours
     * L'EDT commence au jour et à l'heure de la requête
     * et finit 7 jours après
     * On récupère que les DISPOS, si pas de dispo, aucune entrée de faite
     */
    #[Route('/edt/{idKine}/{time}', name: 'edt_kine_week')]
    public function getFullEdt(
        Request $request,
        string $idKine,
        string $time,
        EdtManagerService $edtManagerService
    ): Response
    {
        $movement = $request->get('movement');
        if($movement == 'next')
            $time = strtotime('+7 days', $time);
        elseif($movement == 'prev')
            $time = strtotime('-7 days', $time);
        else
            $time = time();

        $edt = $edtManagerService->getEdtKineForOneWeek($idKine, $time);
        $orderedEdt = array();
        $orderedEdt['idKine'] = $idKine;
        $orderedEdt['timestamp'] = $time;
        foreach($edt as $key => $val) {
            $edtTmp = array(
                'date' => $key,
                'heures' => $val['heures'] ?? [],
                'jour' => $val['jour']
            );

            $orderedEdt['dispos'][] = $edtTmp;
        }

        //dd($orderedEdt);

        return new JsonResponse(json_encode($orderedEdt));
    }
}
