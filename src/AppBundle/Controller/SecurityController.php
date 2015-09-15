<?php

namespace AppBundle\Controller;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $tools = $this->get('security.authentication_utils');

        return $this->render('main/login.html.twig', [
            'error' => $tools->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/admin/login_check", name="admin_login_check")
     */
    public function loginCheckAction()
    {

    }

    /**
     * @Route("/admin/logout", name="admin_logout")
     */
    public function logoutAction()
    {

    }
}
