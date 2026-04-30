<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        Request                $request,
        EntityManagerInterface $entityManager,
        #[Autowire('%serie_poster_dir%')] string $posterDir
    ): Response
    {
        $serie = new Serie();
        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm->handleRequest($request);

        if ($serieForm->isSubmitted() && $serieForm->isValid()) {

            //réupération de l'image et traitement
            $file = $serieForm->get('poster')->getData();
            /**
             * @var UploadedFile $file
             */
            $newFileName = $serie->getName() . "-" . uniqid() . "." . $file->guessExtension();
            //$file->move($this->getParameter('serie_poster_dir'), $newFileName);
            $file->move($posterDir, $newFileName);
            $serie->setPoster($newFileName);

            $serie->setDateCreated(new \DateTime());
            $entityManager->persist($serie);
            $entityManager->flush();
            $this->addFlash('success',$serie->getName() . ' was created !');
            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
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
            $this->addFlash('success',$serie->getName() . ' was deleted !');
        }
        return $this->redirectToRoute('serie_list');
    }


    #[Route('/{id}/update', name: 'update', methods: ["GET", "POST"])]
    public function update(int                    $id,
                           SerieRepository        $serieRepository,
                           Request                $request,
                           EntityManagerInterface $entityManager): Response
    {
        $serie = $serieRepository->find($id);
        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm->handleRequest($request);

        if ($serieForm->isSubmitted() && $serieForm->isValid()) {
            $entityManager->persist($serie);
            $entityManager->flush();
            $this->addFlash('success',$serie->getName() . ' was updated !');
            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render("serie/update.html.twig", [
            'serieForm' => $serieForm
        ]);
    }

}







