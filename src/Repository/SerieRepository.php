<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }


    public function findBestSeries()
    {

        //les series les plus populaires triés par par popularity
        //en DQL

//        $dql = "
//                SELECT s FROM App\Entity\Serie as s
//                WHERE s.popularity > 500
//                ORDER BY s.popularity DESC
//                ";
//
//        $em = $this->getEntityManager();
//        $query = $em->createQuery($dql);

        //avec QueryBuilder
        $qb = $this->createQueryBuilder('s');
        $qb
            ->andWhere("(s.popularity > 500 OR s.overview LIKE :way)")
            ->setParameter('way', '%way%')
            ->addOrderBy('s.popularity', 'DESC');

        $query = $qb->getQuery();
        return $query->getResult();
    }


    public function findBestSeriesWithPagination(int $page)
    {

        $limit = 50;
        $offset = ($page - 1) * $limit;

        $qb = $this->createQueryBuilder('s');
        $qb
            ->join('s.seasons', 'se')
            ->addSelect('se')
            ->addOrderBy('s.popularity', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);


        return $qb->getQuery()->getResult();
    }


}













