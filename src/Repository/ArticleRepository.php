<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }
    
    /**
     * Permet de récupérer un article au hazard
     * 
     * @param   $_online    
     * @param   $_nb        number of articles
     * 
     * @return  Article|false
     */
    public function findRandomArticle($_online, $_nb = 1)
    {
        $result = $this->createQueryBuilder('a')
            ->addSelect('a')
            ->where('a.online = :online')
            ->setParameter('online', $_online)
            ->orderBy('RAND()')
            ->setMaxResults($_nb)
            ->getQuery()
            ->getResult()
        ;
        if(!empty($result) && is_array($result) && array_key_exists(0, $result)) {
            return $result[0];
        }
        return false;
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
