<?php

namespace App\Repository;

use App\Entity\Comments;
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
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
		parent::__construct($registry, Comments::class);
    }

    /**
     * @return VinylMix[] Returns an array of VinylMix objects
     */
	 
    public function commentTaker(int $video = null)
    {
        $queryBuilder = $this->addOrderByQueryBuilder();
        if ($video) {
			$queryBuilder
				->andWhere('comments.video = :vid')
				->setParameter('vid', $video)
				->setMaxResults(10)
				->getQuery()
			;
		}
		return $queryBuilder;
    }
	
	public function commentTakerByUser(int $userId = null)
    {
        $queryBuilder = $this->addOrderByQueryBuilder();
        if ($userId) {
			$queryBuilder
				->andWhere('comments.uploader = :use')
				->setParameter('use', $userId)
				->setMaxResults(10)
				->getQuery()
			;
		}
		return $queryBuilder;
    }
	
	private function addOrderByQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('comments');

        return $queryBuilder->orderBy('comments.createdAt', 'DESC');
    }
	
	private function addOrderByQueryBuilderpfp(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('user');

        return $queryBuilder;
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
