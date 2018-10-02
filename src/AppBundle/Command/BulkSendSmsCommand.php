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
     * @return int|null|void
     * @throws \Twilio\Rest\Api\V2010\Account\TwilioException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Start sms notification.');
        $twilioService = $this->getContainer()->get('twilio.service');
        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $unSentSmsArr = $entityManager->getRepository(SmsProcessor::class)->getUnSentSms();
        /** @var SmsProcessor $sms */
        foreach ($unSentSmsArr as $sms) {
            try {
                $result = $twilioService->sendSms($sms->getPhone(), $sms->getMessage());
                $sms->setStatus($result->status)
                    ->setUpdatedAt(new \DateTime())
                    ->setResult($result);
                $entityManager->flush();
            } catch (\Exception $e) {
                $output->write($e->getMessage());
            }
        }
        $output->write('Finish.');
    }
}
