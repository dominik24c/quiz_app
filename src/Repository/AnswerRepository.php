<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function getAllAnswersByQuiz(Quiz $quiz)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.question','qu')
            ->join('qu.quiz','q')
            ->where('q.id = :quiz_id')
            ->setParameter('quiz_id',$quiz->getId())
            ->getQuery()
            ->getResult();
    }
}
