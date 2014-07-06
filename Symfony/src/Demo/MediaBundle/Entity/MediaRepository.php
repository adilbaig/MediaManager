<?php

namespace Demo\MediaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Repository to query Media items
 */
class MediaRepository extends EntityRepository
{
    /**
     * Get media items by $userId in desc order
     * 
     * @param int $userId
     * @param int $limit (Opt) - Limit results
     * 
     * @return array
     */
    public function getByUserId($userId, $limit = null)
    {
        $sql = 'SELECT m FROM Demo\MediaBundle\Entity\Media m WHERE m.userid = :userid ORDER BY m.id DESC';
        
        $query = $this->getManager()->createQuery($sql);
        $query->setParameter('userid', $userId);
        
        if(!empty($limit)) {
            $query->setMaxResults((int)$limit);
        }
        
        return $query->getResult();
    }
}