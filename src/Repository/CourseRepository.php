<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Course $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Course $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getLastPublishedCourses()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isPublished = :val')
            ->setParameter('val', 1)
            ->orderBy('c.publishedAt', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function getAllCoursesByDate()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isPublished = :val')
            ->setParameter('val', 1)
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchCourses($word)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.title LIKE :val OR c.description LIKE :val')
            ->andWhere('c.isPublished = :publishedVal')
            ->setParameters(array('val' => '%' . $word . '%', 'publishedVal' => 1))
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAllCoursesByStudent($studentId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere(':val MEMBER OF c.chosenBy ')
            ->setParameter('val', $studentId)
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAllCoursesByInstructor($instructorId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.createBy = :val ')
            ->setParameter('val', $instructorId)
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Course[] Returns an array of Course objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
