<?php

namespace AppBundle\Command;

use AppBundle\Entity\NotificationSetting;
use AppBundle\Entity\SmsProcessor;
use AppBundle\Entity\Training;
use AppBundle\Entity\User;
use AppBundle\Entity\NotificationEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BulkSendSmsCommand
 */
class BulkSendSmsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cariba:send-sms')
            ->setDescription('Send sms by twilio.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Twilio\Rest\Api\V2010\Account\TwilioException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Start sms notification.');
        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $twilioService = $this->getContainer()->get('twilio.service');
        $entityManager->getConnection()->beginTransaction();
        $unSentSmsArr = $entityManager->getRepository(SmsProcessor::class)->getUnSentSms();
        try {
            /** @var SmsProcessor $sms */
            foreach ($unSentSmsArr as $sms) {
                $now = new \DateTime();
                $sms->setUpdatedAt($now);
                try {
                    $result = $twilioService->sendSms($sms->getPhone(), $sms->getMessage());
                    $sms->setStatus('sent')
                        ->setResult($result->toArray());
                } catch (\Exception $e) {
                    $result = [
                        'error' => $e->getMessage(),
                    ];
                    $sms->setResult($result);
                }
                $entityManager->flush();
            }
            $entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollBack();
            $output->write($e->getMessage());
        }

        $output->write('Finish.');
    }
}
