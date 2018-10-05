<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
    /**
     * @Route("/login-by-email/{hash}", name="user.login.by.email", requirements={
     *     "hash": "\w+"
     * })
     *
     * @param string  $hash
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginByEmailAction($hash, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user  = $em->getRepository(User::class)->findOneBy([
            'hash' => $hash,
        ]);
        if (!$user) {
            $this->addFlash('warning', 'User not found');

            return $this->redirectToRoute('fos_user_security_login');
        }

        $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
        $this->get("security.token_storage")->setToken($token);
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        $user->setHash($user->generateHash());
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
}
