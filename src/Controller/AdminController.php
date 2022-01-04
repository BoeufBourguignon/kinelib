<?php
/**
 * Pages d'administration
 */

namespace App\Controller;

use App\Repository\EDTRepository;
use App\Repository\UserRepository;
use App\Service\EdtManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * Vérifie que l'user est connecté et qu'il est admin le cas échéant
     */
    private function doRedirect()
    {
        $user = $this->getUser();
        if($user === null || !in_array("ROLE_ADMIN", $user->getRoles()))
            return $this->redirectToRoute('home');
    }

    /**
     * Affiche les emplois du temps de tous les kinés
     */
    #[Route('/admin/edt-kines', name: 'admin_edt_kines')]
    public function edtKines(
        EDTRepository $EDTRepository
    ): Response
    {
        $this->doRedirect();

        $edts = $EDTRepository->getNiceLookingArrayFindAll();

        return $this->render('admin/edt-kines.html.twig', ['edts' => $edts]);
    }

    /**
     * Affiche tous les rendez-vous prévus de tous les kinés pour la semaine
     */
    #[Route('/admin/rdv-kines', name: 'admin_rdv_kines')]
    public function rdvKines(
        EdtManagerService $edtManagerService
        , UserRepository $userRepository
    ): Response
    {
        $this->doRedirect();

        $rdvs = array();
        $kines = $userRepository->findByRole('kine');
        foreach($kines as $kine) {
            $rdvs[$kine->getId()] = $edtManagerService->getRdvKineForOneWeek($kine->getId());
        }

        return $this->render('admin/rdv-kines.html.twig', ['kines' => $kines, 'rdvs' => $rdvs]);
    }
}
