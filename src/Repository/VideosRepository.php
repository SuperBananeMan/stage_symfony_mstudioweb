<?php

namespace App\Repository;

use App\Entity\Videos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @extends ServiceEntityRepository<Videos>
 *
 * @method Videos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videos[]    findAll()
 * @method Videos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Videos::class);
    }
	
	public function add(Videos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
	
	    public function remove(Videos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return VinylMix[] Returns an array of VinylMix objects
     */
	 
    public function createOrderedByQueryBuilder($session, string $genre = null, int $video = null)
    {
        $queryBuilder = $this->addOrderByQueryBuilder();
        if ($genre) {
			if ($genre == strval($session->get('id')[0]['id'])){
				$queryBuilder->andWhere('mix.uploader = :genre')
					->setParameter('genre', $genre);
			}
			else{
				$queryBuilder->andWhere('mix.genre = :genre')
					->setParameter('genre', $genre);
			}
		}
		return $queryBuilder;
    }
	
	private function addOrderByQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('mix');

        return $queryBuilder->orderBy('mix.createdAt', 'DESC');
    }

//    /**
//     * @return Videos[] Returns an array of Videos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Videos
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}