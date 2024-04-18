<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Filter\ProductFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Produit::class);
    }

    public function findAllOrderByDate(string $sort = 'DESC'): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', $sort)
            ->getQuery()
            ->getResult();
    }

    public function findListShop(int $page = 1, int $maxPerPage = 6): PaginationInterface
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.enable = :enable')
            ->setParameter('enable', true)
            ->join('p.taxe', 't')
            ->leftJoin('p.categories', 'c')
            ->orderBy('p.title', 'ASC')
            ->getQuery();

        return $this->paginator->paginate(
            $query,
            $page,
            $maxPerPage
        );
    }

    public function findFilterListShop(ProductFilter $filter): array
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.enable = :enable')
            ->setParameter('enable', true)
            ->join('p.taxe', 't')
            ->leftJoin('p.categories', 'c');

        if (!empty($filter->getQuery())) {
            $query
                ->andWhere('p.title LIKE :query')
                ->setParameter('query', '%' . $filter->getQuery() . '%');
        }

        if ($filter->getMin()) {
            $query
                ->andWhere('p.priceHT * (1 + t.rate) >= :min')
                ->setParameter('min', $filter->getMin());
        }

        if ($filter->getMax()) {
            $query
                ->andWhere('p.priceHT * (1 + t.rate) <= :max')
                ->setParameter('max', $filter->getMax());
        }

        if (!empty($filter->getTags())) {
            $query
                ->andWhere('c.id IN (:tags)')
                ->setParameter('tags', $filter->getTags());
        }

        $query
            ->orderBy($filter->getSort(), $filter->getOrder())
            ->getQuery();

        $paginate = $this->paginator->paginate(
            $query,
            $filter->getPage(),
            6
        );

        $subQuery = $query->select('MIN(p.priceHT * (1 + t.rate)) as min, MAX(p.priceHT * (1 + t.rate)) as max')
            ->getQuery()
            ->getScalarResult();

        return [
            'data' => $paginate,
            'min' => (int) $subQuery[0]['min'],
            'max' => (int) $subQuery[0]['max'],
        ];
    }

    //    /**
    //     * @return Produit[] Returns an array of Produit objects
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

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
