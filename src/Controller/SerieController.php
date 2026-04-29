<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie_')]
final class SerieController extends AbstractController
{
    #[Route('', name: 'list')]
    #[Route('/list', name: 'list2')]
    public function list(SerieRepository $serieRepository): Response
    {
//        $series = $serieRepository->findAll();
//        $series = $serieRepository->findBy(["status" => "ended"], ["name" => "ASC"]);

//        $series = $serieRepository->findBy([], ['popularity' => 'DESC']);
        $series = $serieRepository->findBestSeries();

        return $this->render('serie/list.html.twig', [
            "series" => $series
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
    public function create(EntityManagerInterface $entityManager): Response
    {

        $serie = new Serie();
        $serie
            ->setBackdrop("backdrop.png")
            ->setDateCreated(new \DateTime())
            ->setFirstAirDate(new \DateTime('-6 month'))
            ->setName("The Witcher")
            ->setGenres('Fantastique')
            ->setLastAirDate(new \DateTime('-1 month'))
            ->setPopularity(5000)
            ->setPoster('poster.png')
            ->setStatus('returning')
            ->setTmdbId(123456)
            ->setVote(8);

        dump($serie);

        $entityManager->persist($serie);
        $entityManager->flush();

        dump($serie);
        $serie->setName("Buffy contre les vampires");
        $entityManager->persist($serie);
        $entityManager->flush();

        $entityManager->remove($serie);
        $entityManager->flush();

        //TODO créer une nouvelle série avec un formulaire
        return $this->render('serie/create.html.twig');
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







