<?php

namespace App\Controller;

use App\Repository\EDTRepository;
use App\Repository\UserRepository;
use App\Service\EdtManagerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class KineController extends AbstractController
{
    /**
     * Récupère l'EDT du kiné connecté et ses RDVs des 7 prochains jours
     */
    #[Route('/kine/edt', name: 'kine_edt')]
    public function edtKine(
        EDTRepository $EDTRepository
        , UserRepository $userRepository
        , EdtManagerService $edtManagerService
    ): Response
    {
        $user = $this->getUser();
        if ($user === null || !in_array("ROLE_KINE", $user->getRoles()))
            return $this->redirectToRoute('home');

        $edt = $EDTRepository->getNiceLookingArrayFindByKine($user, $userRepository);

        $rdvs = $edtManagerService->getRdvKineForOneWeek(
            $userRepository
                ->findOneBy([
                    'email' => $user->getUserIdentifier()
                ])
                ->getId(),
            time()
        );
        //dd($rdvs);

        return $this->render('kine/kine-edt.html.twig', ['edt' => $edt, 'rdvs' => $rdvs]);
    }
}