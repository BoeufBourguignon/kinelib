<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(UserRepository $userRepository): Response
    {
        if ($this->getUser() === null)
            return $this->redirectToRoute('login');

        $kines = $userRepository->findByRole('kine');

        return $this->render('home/index.html.twig', ['kines' => $kines]);
    }
}
