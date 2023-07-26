<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
		parent::__construct($registry, User::class);
    }

	/**
	* @return User[] Returns an array of User objects
	*/
	
	public function findIdByUserName($value): array
	{
		return $this->createQueryBuilder('table')
			->select('table.id')
			->andWhere('table.username = :val')
			->setParameter('val', $value)
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	
	public function findByUserNameField($value): array
	{
		return $this->createQueryBuilder('table')
			->andWhere('table.username = :val')
			->setParameter('val', $value)
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	
	public function findByEmailField($value): array
	{
		return $this->createQueryBuilder('table')
			->andWhere('table.email = :val')
			->setParameter('val', $value)
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	
	public function findByPasswordField($value): array
	{
		return $this->createQueryBuilder('table')
			->andWhere('table.password = :val')
			->setParameter('val', $value)
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	
	public function findByPfpNameField($value): array
	{
		return $this->createQueryBuilder('table')
			->select('table.pfpName')
			->andWhere('table.username = :val')
			->setParameter('val', $value)
			->setMaxResults(10)
			->getQuery()
			->getResult()
		;
	}
	
	public function commentTakerpfp($value)
    {
		return $this->createQueryBuilder('table')
			->andWhere('table.id = :val')
			->setParameter('val', $value)
			->setMaxResults(10)
			->getQuery()
		;
	}

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('table')
//            ->andWhere('table.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
