<?php

namespace App\Repository;

use App\DTO\ClientSearchDTO;
use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function searchClients(ClientSearchDTO $clientSearchDTO): array
    {
        $queryBuilder = $this->createQueryBuilder('c');
        if ($clientSearchDTO->getTelephone()) {
            $queryBuilder->andWhere('c.telephone = :telephone')->setParameter('telephone', $clientSearchDTO->getTelephone());
        }
        if ($clientSearchDTO->getUsername()) {
            $queryBuilder->andWhere('c.username = :username')->setParameter('username', $clientSearchDTO->getUsername());
        }
        return $queryBuilder->getQuery()->getResult();
    }

    public function showPaginated(int $page, int $limit)
    {

        $offset = ($page - 1) * $limit;
        return $this->createQueryBuilder('c')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countClients($telephone = null, $username = null): int
    {
        if ($telephone != null && $username == null) {
            return $this->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.telephone = :telephone')
                ->setParameter('telephone', $telephone)
                ->getQuery()
                ->getSingleScalarResult();
        } elseif ($telephone == null && $username != null) {
            return $this->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.username = :username')
                ->setParameter('username', $username)
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            return $this->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->getQuery()
                ->getSingleScalarResult();
        }
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
