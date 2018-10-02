<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class SmsProcessorRepository
 */
class SmsProcessorRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getUnSentSms()
    {
        $result = $this->createQueryBuilder('sms_processor')
            ->select('sms_processor')
            ->where('sms_processor.status IS NULL')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
