<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\Solution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function getTheMostPopularQuizzes($numberOfQuizzes)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('q.id, q.title, q.description,c.name AS category, COUNT(q.id) AS solved_quizzes')
            ->from(Solution::class,'s')
            ->join('s.quiz','q')
            ->join('q.category','c')
            ->groupBy('q.id')
            ->orderBy('solved_quizzes','DESC')
            ->setMaxResults($numberOfQuizzes)
            ->getQuery()
            ->getScalarResult();
    }

    public function getQuizzesByUser(UserInterface $user,$pageSize,$offset)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user_id')
            ->setParameter('user_id',$user->getId())
            ->orderBy('q.createdAt','DESC')
            ->setMaxResults($pageSize)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function getQuizzesByTitle($flag=true,$pageSize = 5,$offset=0,$title=null)
    {
        $queryBuilder = $this->createQueryBuilder('q');

        if($title != null){
            $title .= "%";
            $queryBuilder
                ->where('q.title LIKE :title')
                ->setParameter('title',$title);
        }

        $queryBuilder->orderBy('q.createdAt','DESC');

        if($flag){
            $queryBuilder->setMaxResults($pageSize)->setFirstResult($offset);
        }

        return $queryBuilder
                ->getQuery()
                ->getResult();
    }
}
