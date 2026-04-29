<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie_')]
final class SerieController extends AbstractController
{

    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'])]
    public function list(SerieRepository $serieRepository, int $page = 1): Response
    {
//        $series = $serieRepository->findAll();
//        $series = $serieRepository->findBy(["status" => "ended"], ["name" => "ASC"]);
//        $series = $serieRepository->findBy([], ['popularity' => 'DESC']);
//        $series = $serieRepository->findBestSeries();

        $nbSeries = $serieRepository->count();
        $maxPage = ceil($nbSeries / 50);

        //gestion des pages coté back
        if ($page < 1) {
            return $this->redirectToRoute('serie_list');
        } elseif ($page > $maxPage) {
            return $this->redirectToRoute('serie_list', ['page' => $maxPage]);
        }

        $series = $serieRepository->findBestSeriesWithPagination($page);

        return $this->render('serie/list.html.twig', [
            "series" => $series,
            'currentPage' => $page,
            'maxPage' => $maxPage
        ]);
    }


    #[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id, SerieRepository $serieRepository): Response
    {
        //$serie = $serieRepository->findOneBy(['id' => $id]);
        $serie = $serieRepository->find($id);

        if (!$serie) {
            throw $this->createNotFoundException("Ooops ! Serie not found !");
        }

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/create', name: 'create', methods: ["GET", "POST"])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager): Response
    {
        $serie = new Serie();
        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm->handleRequest($request);

        if($serieForm->isSubmitted()){
            $serie->setDateCreated(new \DateTime());
            $entityManager->persist($serie);
            $entityManager->flush();
        }

        return $this->render('serie/create.html.twig', [
            'serieForm' => $serieForm
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ["GET"])]
    public function delete(int                    $id,
                           SerieRepository        $serieRepository,
                           EntityManagerInterface $entityManager): Response
    {
        $serie = $serieRepository->find($id);

        if ($serie) {
            $entityManager->remove($serie);
            $entityManager->flush();
        }
        return $this->redirectToRoute('serie_list');
    }
}







