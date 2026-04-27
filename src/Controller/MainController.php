<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('', name: 'main_home')]
    public function home(): Response
    {
        $username = "<h1>Sylvain</h1>";

        return $this->render('main/home.html.twig', [
            'username' => $username
        ]);
    }


    #[Route('/test', name: 'main_test')]
    public function test(): Response
    {
        return $this->render('main/test.html.twig');
    }
}













