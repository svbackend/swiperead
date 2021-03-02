<?php

namespace App\Repository;

use App\Entity\User\UserNetwork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserNetwork|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserNetwork|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserNetwork[]    findAll()
 * @method UserNetwork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserNetworkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNetwork::class);
    }

    public function findOne(string $network, string $networkId): ?UserNetwork
    {
        try {
            return $this->createQueryBuilder('UserNetwork')
                ->join('UserNetwork.user', 'User')
                ->addSelect('User')
                ->where('UserNetwork.network = :network')
                ->andWhere('UserNetwork.networkId = :networkId')
                ->setParameters([
                    'network' => $network,
                    'networkId' => $networkId,
                ])
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
