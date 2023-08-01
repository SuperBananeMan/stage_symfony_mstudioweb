<?php

namespace App\Repository;

use App\Entity\Adminforum;
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
class AdminforumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
		parent::__construct($registry, Adminforum::class);
    }
	
	public function adminforumTakerAll()
    {
        $queryBuilder = $this->addOrderByQueryBuilderAdminforum();
		return $queryBuilder;
    }
	
	public function adminforumTakerPage(string $slug = null)
    {
        $queryBuilder = $this->addOrderByQueryBuilderAdminforumPage();
		if($slug){
			$queryBuilder->andWhere('adminforumpage.slug_page = :slug')
						->setParameter('slug', $slug);
		}
		return $queryBuilder;
    }
	
	private function addOrderByQueryBuilderAdminforum(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('adminforum');
		
        return $queryBuilder->orderBy('adminforum.createdAt', 'DESC');
    }
	
	private function addOrderByQueryBuilderAdminforumPage(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('adminforumpage');
		
        return $queryBuilder->orderBy('adminforumpage.createdAt', 'DESC');
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
