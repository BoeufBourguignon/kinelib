<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    #[Route('/edt/{idKine}/{week}', name: 'edt_kine_week')]
    public function getEdtPerWeek(string $idKine, string $week = null): Response
    {
        if (!is_int($week) || $week < 0 || $week > 53) {
            $week = date('W', time());
        }


        return $this->render('ajax/index.html.twig', [
            'controller_name' => 'AjaxController',
        ]);
    }
}
