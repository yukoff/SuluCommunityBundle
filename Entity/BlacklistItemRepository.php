<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CommunityBundle\Entity;

use Sulu\Component\Persistence\Repository\ORM\EntityRepository;

/**
 * Entity-Repository for blacklist-items.
 */
class BlacklistItemRepository extends EntityRepository
{
    /**
     * Returns items which matches given email.
     *
     * @param string $email
     *
     * @return BlacklistItem[]
     */
    public function findBySender($email)
    {
        $queryBuilder = $this->createQueryBuilder('entity');
        $dbPlatformName = $queryBuilder->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
        if (preg_match('/postgresql/', $dbPlatformName)) {
            $queryBuilder->getEntityManager()
                ->getConfiguration()
                ->addCustomStringFunction('REGEXP', 'Sulu\Bundle\CommunityBundle\DoctrineExtensions\Query\Postgresql\Regexp');
        }
        // NOTE: SQLite seems to have similar syntax, so this might or might not work for SQLite too
        $queryBuilder->where('REGEXP(:email, entity.regexp) = true')
            ->setParameter('email', $email);

        return $queryBuilder->getQuery()->getResult();
    }
}
