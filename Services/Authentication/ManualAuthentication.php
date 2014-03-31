<?php

namespace CL\PersonaUserBundle\Services\Authentication;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * This class helps the user to authenticate manually
 *
 * @author julien.fastre@champs-libres.coop
 */
class ManualAuthentication implements ContainerAwareInterface{
    
    /**
     *
     * @var \Symfony\Component\Security\Core\SecurityContextInterface 
     */
    public $securityContext;
    
    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface 
     */
    public $eventDispatcher;
    
    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request 
     */
    public $request;
    
    
    public function authenticate(UserInterface $user) {
        //authenticate the user
        $token = new \CL\PersonaUserBundle\Security\Token\PersonaUserToken();
        $token->setUser($user);
        $token->setAuthenticated(true);
        $token->email = $user->getUsername();

        $this->securityContext->setToken($token);
        
        $event = new InteractiveLoginEvent($this->request, $token);
        
        $this->eventDispatcher
              ->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, 
                    $event);
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->securityContext = $container->get('security.context');
        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->request = $container->get('request');
    }

}
