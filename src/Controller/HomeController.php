<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\EDTRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(UserRepository $userRepository, EDTRepository $EDTRepository): Response
    {
        if ($this->getUser() === null)
            return $this->redirectToRoute('login');

        $kines = $userRepository->findByRole('kine');
        $edtKines = $EDTRepository->getNiceLookingArrayFindAll();
        date_default_timezone_set('Europe/Paris');
        $todayDay = date('w', time()) - 1 != -1 ? date('w', time()) - 1 : 6;
        //$todayDay = 1;
        $todayHour = date('H:i', time());

        $edtBienCouscous = array();
        foreach($edtKines as $idKine => $contentEdt) {
            foreach($contentEdt['edt'] as $idJour => $jour) {
                if ($idJour >= $todayDay) {
                    if (count($jour['periode']) == 2) { // Il y a le matin et l'aprem
                        // Matin
                        $heureDebutMatin = date('H:i', strtotime($jour['periode']['matin']['heureDebut']));
                        $heureFinMatin = date('H:i', strtotime($jour['periode']['matin']['heureFin']));
                        while ($heureDebutMatin < $heureFinMatin) {
                            if ($idJour != $todayDay || $heureDebutMatin > $todayHour) {
                                $edtBienCouscous[$idKine][$jour['nom']][] = $heureDebutMatin;
                            }
                            $heureDebutMatin = date('H:i', strtotime('+30 minutes', strtotime($heureDebutMatin)));
                        }
                        $edtBienCouscous[$idKine][$jour['nom']][] = 'br';
                        // Aprem
                        $heureDebutAprem = date('H:i', strtotime($jour['periode']['aprem']['heureDebut']));
                        $heureFinAprem = date('H:i', strtotime($jour['periode']['aprem']['heureFin']));
                        while ($heureDebutAprem < $heureFinAprem) {
                            if ($idJour != $todayDay || $heureDebutAprem > $todayHour) {
                                $edtBienCouscous[$idKine][$jour['nom']][] = $heureDebutAprem;
                            }
                            $heureDebutAprem = date('H:i', strtotime('+30 minutes', strtotime($heureDebutAprem)));
                        }
                    } else {
                        $dateDebut = date('H:i', strtotime($jour['periode']['all']['heureDebut']));
                        $dateFin = date('H:i', strtotime($jour['periode']['all']['heureFin']));
                        while ($dateDebut < $dateFin) {
                            if ($idJour != $todayDay || $dateDebut > $todayHour) {
                                $edtBienCouscous[$idKine][$jour['nom']][] = $dateDebut;
                            }
                            $dateDebut = date('H:i', strtotime('+30 minutes', strtotime($dateDebut)));
                        }
                    }
                }
            }
        }

        return $this->render('home/index.html.twig', ['kines' => $kines, 'edtKines' => $edtBienCouscous]);
    }

    #[Route('/kine/edt', name: 'kine_edt')]
    public function edtKine(EDTRepository $EDTRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if ($user=== null)
            return $this->redirectToRoute('login');

        $edt = $EDTRepository->getNiceLookingArrayFindByKine($user, $userRepository);

        return $this->render('home/kine-edt.html.twig', ['edt' => $edt]);
    }
}
