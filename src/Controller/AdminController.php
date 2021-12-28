<?php

namespace App\Controller;

use App\Repository\EDTRepository;
use App\Repository\UserRepository;
use App\Service\EdtManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $user = $this->getUser();
        if($user === null || !in_array("ROLE_ADMIN", $user->getRoles()))
            return $this->redirectToRoute('home');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/edt-kines', name: 'admin_edt_kines')]
    public function edtKines(EDTRepository $EDTRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if($user === null || !in_array("ROLE_ADMIN", $user->getRoles()))
            return $this->redirectToRoute('home');

        $edts = $EDTRepository->getNiceLookingArrayFindAll();
        //dd($edts);

        return $this->render('admin/edt-kines.html.twig', ['edts' => $edts]);
    }

    #[Route('/admin/rdv-kines', name: 'admin_rdv_kines')]
    public function rdvKines(EdtManagerService $edtManagerService, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if($user === null || !in_array("ROLE_ADMIN", $user->getRoles()))
            return $this->redirectToRoute('home');

        $rdvs = array();
        $kines = $userRepository->findByRole('kine');
        foreach($kines as $kine) {
            $rdvs[$kine->getId()] = $edtManagerService->getRdvKineForOneWeek($kine->getId());
        }
        // idKine
        //   date
        //     jour
        //     rdvs
        //       0 => heure, nom, prenom

        //dd($kines,$rdvs);

        return $this->render('admin/rdv-kines.html.twig', ['kines' => $kines, 'rdvs' => $rdvs]);
    }
}
