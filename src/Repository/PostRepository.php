<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    protected $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

    public function findAllPosts(int $page)
    {
        $dbquery = $this->createQueryBuilder('p')
        ->leftJoin('p.user', 'u')        
        ->addSelect('u')
        ->leftJoin('p.usersThatLike', 'l')
        ->addSelect('COUNT(l) AS HIDDEN likes')
        ->orderBy('likes', 'DESC')
        ->groupBy('p')
        ->getQuery()
        ->getResult();

        return $this->paginator->paginate($dbquery,$page,3);

    }

    public function findAllUserPosts(int $page, $userId)
    {
        $dbquery = $this->createQueryBuilder('p')
        ->leftJoin('p.user', 'u')
        ->addSelect('u')
        ->addSelect('COUNT(l) AS HIDDEN likes')
        ->leftJoin('p.usersThatLike', 'l')
        ->orderBy('likes', 'DESC')
        ->where('p.user = :id')
        ->setParameter('id', $userId)
        ->groupBy('p')
        ->getQuery()        
        ->getResult()
        ;

        return $this->paginator->paginate($dbquery, $page, 3);
    }

    public function isLiked($authUser, $postId): array
    {
        return $this->createQueryBuilder('p')
                    ->select('p.id')
                    ->andWhere('p.id = :postId')
                    ->andWhere('usersThatLike.id = :authUser')
                    ->innerJoin('p.usersThatLike', 'usersThatLike')
                    ->setParameter('postId', $postId)
                    ->setParameter('authUser', $authUser)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult()
                ;
    }

    public function isDisliked($authUser, $postId): array 
    {
        return $this->createQueryBuilder('p')
                    ->select('p.id')
                    ->andWhere('p.id = :postId')
                    ->andWhere('usersThatLike.id = :authUser')
                    ->innerJoin('p.usersThatDontLike', 'usersThatLike')
                    ->setParameter('postId', $postId)
                    ->setParameter('authUser', $authUser)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult()
                ;
    }
    public function searchPosts(string $query)
    {
        $querybuilder = $this->createQueryBuilder('p');

        $searchTerms = $this->prepareQuery($query);
        foreach($searchTerms as $key => $term)
        {
            $querybuilder
                ->orWhere('p.title LIKE :t_'.$key)
                ->orWhere('p.content LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.trim($term).'%');
        }
        $dbQuery = $querybuilder
                    ->select('p.title', 'p.id')
                    ->getQuery()
                    ->getResult();
        return $dbQuery;
    }

    private function prepareQuery(string $query): array
    {
        $terms = array_unique(explode(' ', $query));
        return array_filter($terms, function($term){
            return 2 <= mb_strlen($term);
        });
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
