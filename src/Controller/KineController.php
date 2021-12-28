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
    #[Route('/kine/edt', name: 'kine_edt')]
    public function edtKine(EDTRepository $EDTRepository, UserRepository $userRepository, EdtManagerService $edtManagerService): Response
    {
        $user = $this->getUser();
        if ($user === null)
            return $this->redirectToRoute('login');

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