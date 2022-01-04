<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\RDV;
use App\Repository\EDTRepository;
use App\Repository\RDVRepository;
use App\Service\EdtManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    /**
     * Récupère tous les prochaines rendez-vous disponibles de tous les kinés
     */
    #[Route('/', name: 'home')]
    public function index(
        UserRepository $userRepository
      , EdtManagerService $edtManagerService
    ): Response
    {
        if ($this->getUser() === null)
            return $this->redirectToRoute('login');

        $kines = $userRepository->findByRole('kine');
        $edtKines = array();
        $nextDispoPerKine = array();
        foreach($kines as $kine) {
            $edtKines[$kine->getId()] = $edtManagerService->getEdtKineForOneWeek($kine->getId());

            // On récupère la prochaine disponibilité de chaque kiné
            $edtKineAsIndexArray = array_keys($edtKines[$kine->getId()]);
            $i = 0;
            while ($i < count($edtKineAsIndexArray) - 1 && !isset($edtKines[$kine->getId()][$edtKineAsIndexArray[$i]]['heures'])) {
                $i++;
            }
            if (isset($edtKines[$kine->getId()][$edtKineAsIndexArray[$i]]['heures'])) {
                $nextDispoPerKine[$kine->getId()]['heure'] = $edtKines[$kine->getId()][$edtKineAsIndexArray[$i]]['heures'][0];
                $nextDispoPerKine[$kine->getId()]['date'] = $edtKineAsIndexArray[$i] == date('d/m/Y', time()) ? 'Aujourd\'hui' : $edtKineAsIndexArray[$i];
            }
        }

        return $this->render('home/index.html.twig', ['currStartDate' => time(), 'kines' => $kines, 'edtKines' => $edtKines, 'dispos' => $nextDispoPerKine]);
    }

    /**
     * Récupère les rendez-vous de l'user connecté
     */
    #[Route('/rdv', name: 'rdvs')]
    public function mesRdv(
        UserRepository $userRepository
        , RDVRepository $RDVRepository
    ): Response
    {
        $user = $this->getUser();
        if ($user === null)
            return $this->redirectToRoute('login');

        $sesRdvs = $RDVRepository
            ->getRdvsByUser($userRepository
                ->findOneBy([
                    'email' => $user->getUserIdentifier()
                ])
            );

        return $this->render('home/rdvs.html.twig', ['rdvs' => $sesRdvs]);
    }

    /**
     * Confirmer l'ajout d'un rendez-vous
     */
    #[Route('/rdv/add', name: 'add_rdv')]
    public function addRdv(
        Request $request
        , EntityManagerInterface $entityManager
        , UserRepository $userRepository
    ): Response
    {
        $user = $this->getUser();
        if ($user === null)
            return $this->redirectToRoute('login');

        // Récupère toutes les infos du RDV
        $kine = $userRepository
            ->find($request->request->get('kine'));
        $userObj = $userRepository
            ->findOneBy([
                'email' => $user->getUserIdentifier()
            ]);
        $heure = $request->request->get('heure');
        $date = $request->request->get('date');
        if(strlen($date) == 10) {
            $dateAsArray = explode('/', $date);
            $date = mktime(0, 0, 0, $dateAsArray[1], $dateAsArray[0], $dateAsArray[2]);
        }

        $rdv = new RDV();
        $rdv
            ->setKine($kine)
            ->setUser($userObj)
            ->setDate((new \DateTime)->setTimestamp($date))
            ->setHeureDebut(($heure));

        // Si le formulaire de confirmation a été envoyé on ajoute le RDV
        if($request->request->get('isSent') !== null) {

            $entityManager->persist($rdv);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/add_rdv.html.twig', [
            'rdvToAdd' => $rdv
        ]);
    }
}
