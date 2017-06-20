<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends EntityRepository
{
    public function findAllLoginsCount()
    {
        $dql = "SELECT SUM(c.loginCount) AS all_logins FROM AppBundle:Client c";
        return $this->getEntityManager()
            ->createQuery(
                $dql
            )
            ->getSingleScalarResult();
    }

    public function findAllOffersCount(){
        $dql = "SELECT SUM(c.generatedOffersCount) AS all_offers FROM AppBundle:Client c";
        return $this->getEntityManager()
            ->createQuery(
                $dql
            )
            ->getSingleScalarResult();
    }
}
