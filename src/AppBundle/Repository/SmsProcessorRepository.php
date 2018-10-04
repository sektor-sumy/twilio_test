<?php
namespace AppBundle\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;

/**
 * Class SmsProcessorRepository
 */
class SmsProcessorRepository extends EntityRepository
{
    /**
     * @param $rows
     *
     * @return array
     *
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getUnSentSms($rows)
    {
        $result = $this->createQueryBuilder('sms_processor')
            ->select('sms_processor')
            ->where('sms_processor.status IS NULL')
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->setMaxResults($rows)
            ->getResult()
        ;

        return $result;
    }
}
