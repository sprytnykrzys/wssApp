<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getByToken()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u FROM AppBundle:User u ORDER BY u.id ASC'
            )
            ->getResult();
    }
    public function findAllClients(){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u FROM AppBundle:User u WHERE UPPER(u.role) NOT LIKE \'ADMIN\' ORDER BY u.id ASC'
            )
            ->getResult();
    }
    public function findRecentlyLoggedUsers(){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u FROM AppBundle:User u WHERE UPPER(u.role) NOT LIKE \'ADMIN\' ORDER BY u.lastLogin DESC'
            )
            ->setMaxResults(20)
            ->getResult();
    }
    public function getByAuthData( $uid, $token, $ip){
        $em = $this->getEntityManager();
        return $em->getRepository('AppBundle\Entity\User')->findBy(array(
            'id' => $uid,
            'token' => $token,
            'last_host' => $ip
        ));
    }
    public function getByLoginCredentials( $email, $password ){
        $em = $this->getEntityManager();
        $result = $em->getRepository('AppBundle\Entity\User')->findBy(array(
            'email' => $email,
            'password' => $password,
        ));
        if(count($result) == 0){
            return null;
        }
        return $result[0];
    }
}
