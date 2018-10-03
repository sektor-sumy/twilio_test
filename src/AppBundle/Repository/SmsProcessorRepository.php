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
     * @return array
     *
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getUnSentSms()
    {
        $result = $this->createQueryBuilder('sms_processor')
            ->select('sms_processor')
            ->where('sms_processor.status IS NULL')
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getResult()
        ;

        return $result;
    }
}
