<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BulkSendSmsWithLinkCommand
 */
class BulkSendSmsWithLinkCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cariba:send-sms-with-link')
            ->addOption('phone', null, InputOption::VALUE_REQUIRED, 'Pleas enter phone!')
            ->setDescription('Send sms with link by twilio.');
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
        $phone = $input->getOption('phone');

        $output->write('Start email notification.');
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'phone' => $phone,
        ]);

        if ($user) {
            try {
                $twilioService = $this->getContainer()->get('twilio.service');
                $link = $this->getContainer()->get('router')->generate('user.login.by.email', ['hash' => $user->getHash()]);
                $twilioService->sendSms($user->getPhone(), $link);
            } catch (\Exception $e) {
                $output->write($e->getMessage());
            }
            $output->write('Finish.');
        } else {
            $output->write('User not found.');
        }
    }
}
