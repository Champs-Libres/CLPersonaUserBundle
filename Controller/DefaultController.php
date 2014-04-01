<?php

namespace CL\PersonaUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use CL\PersonaUserBundle\Services\Persona\PersonaAuthenticationFailedException;
use CL\PersonaUserBundle\CLPersonaUserBundle;

/**
 * Basic controller that handle operation of login / logout
 */
class DefaultController extends Controller
{    
    
    public function personaLoginFormAction() {
        return $this->render('CLPersonaUserBundle:Default:Login.html.twig');
    }
    
    public function personaLoginCheckAction() {
        if ($this->get('security.context')->getToken()->getUser() === NULL) {
           throw new \Exception('There are no user registered. Did you forget to create a firewall for /persona ?');
        }

        $response = new Response(json_encode(array(
           CLPersonaUserBundle::KEY_EMAIL_SESSION => 
              $this->get('security.context')->getToken()->getUser()->getPersonaId()
        )));
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        return $response;
    }
    
   

    public function personaLogoutAction() {
        $response = new Response('ok');
        return $response;
    }
}
