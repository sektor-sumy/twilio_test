<?php


namespace AppBundle\Service;

use AppBundle\Entity\User;
use RageNotificationBundle\Message\Email;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EmailService
 */
class EmailService
{
    /**
     * @var Email
     */
    private $notification;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * EmailService constructor.
     * @param Email                  $notification
     * @param EntityManagerInterface $em
     */
    public function __construct(Email $notification, EntityManagerInterface $em)
    {
        $this->notification = $notification;
        $this->em = $em;
    }

    /**
     * @param User   $user
     * @param string $message
     */
    public function sendCustomMail(User $user, $message)
    {
        $this->notification
            ->setTo($user->getEmail())
            ->setTemplate('custom-message', [
                'user' => $user,
                'message' => $message,
            ])->sendMessage();
    }
}
