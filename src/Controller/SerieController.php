<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie_')]
final class SerieController extends AbstractController
{
    #[Route('', name: 'list')]
    #[Route('/list', name: 'list2')]
    public function list(): Response
    {
        //TODO renvoyer la liste des séries
        return $this->render('serie/list.html.twig');
    }


    #[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id): Response
    {

        dump($id);
        //TODO renvoyer une série
        return $this->render('serie/detail.html.twig');
    }

    #[Route('/create', name: 'create', methods: ["GET", "POST"])]
    public function create(): Response
    {
        //TODO créer une nouvelle série avec un formulaire
        return $this->render('serie/create.html.twig');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ["GET"])]
    public function delete(): Response
    {
        //TODO supprimer une série
        return $this->render('serie/list.html.twig');
    }
}







