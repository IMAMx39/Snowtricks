<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findCommentsByTrick($trick): array
    {
        return $this->findBy(["trick" => $trick], ["createdAt" => "DESC"]);
    }

    public function findComment($trick)
    {
        if ($trick instanceof Trick) {
            $object = 'trick';
        }

        return $this->createQueryBuilder('c')
            ->andWhere('c.' . $object . '= :val')
            ->setParameter('val', $trick->getId())
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()->getResult();

    }

    public function getCommentPaginator(Trick $trick, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

}
