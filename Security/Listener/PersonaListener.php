<?php

namespace CL\PersonaUserBundle\Security\Listener;

use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use CL\PersonaUserBundle\Services\Persona\PersonaService;
use CL\PersonaUserBundle\Services\Persona\PersonaAuthenticationFailedException;
use CL\PersonaUserBundle\Services\Persona\PersonaAuthenticationNoAssertionException;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use CL\PersonaUserBundle\Security\Token\PersonaUserToken;
use CL\PersonaUserBundle\CLPersonaUserBundle;
use CL\PersonaUserBundle\Security\Provider\PersonaIdNotExistingException;

/**
 * D
 *
 * @author julien.fastre@champs-libres.coop
 */
class PersonaListener implements ListenerInterface {
    
    /**
     *
     * @var \CL\PersonaUserBundle\Services\Persona\PersonaService
     */
    private $personaService;
    
    /**
     *
     * @var \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
     */
    private $authenticationManager;
    
    /**
     *
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;
    
    /**
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session;
     */
    private $session;
    
    /**
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router; 
     */
    private $router;
    
    private $formCreationRoute;
    
    public function __construct(PersonaService $personaService, 
          AuthenticationManagerInterface $authenticationManager,
          SecurityContextInterface $securityContext,
          SessionInterface $session,
          Router $router,
          $formCreationRoute) {
        
        $this->personaService = $personaService;
        $this->authenticationManager = $authenticationManager;
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->router = $router;
        $this->formCreationRoute = $formCreationRoute;
        
    }
    
    
    public function handle(GetResponseEvent $event) {
        $request = $event->getRequest();
        
        if ($request->query->has('assertion')) {
            try {
                
                
                $token = new PersonaUserToken();
                
                $authToken = $this->authenticationManager->authenticate($token);
                $this->securityContext->setToken($authToken);
                
                return;
            } catch (PersonaIdNotExistingException $e) {
                
                //store the user into session, then throw an "goto" in response
                $this->session->set(CLPersonaUserBundle::KEY_EMAIL_SESSION, 
                      $e->personaId);
                
                //prepare the response
                $urlToRedirect = $this->router
                      ->generate($this->formCreationRoute, array(), true);
                $contentArray = array(
                   CLPersonaUserBundle::KEY_EMAIL_SESSION => $e->personaId,
                   CLPersonaUserBundle::KEY_ROUTE_GOTO => $urlToRedirect
                      );
                $response = new Response(json_encode($contentArray));
                $response->headers->set('Content-Type', 'application/json');
                      
                $event->setResponse($response);
                return;
                      
            } catch (PersonaAuthenticationNoAssertionException $e) {
                $event->setResponse($this->getResponseForbidden('no assertion__')); 
                
            } catch (PersonaAuthenticationFailedException $e ) {
                $event->setResponse($this->getResponseForbidden($e->getMessage()));
            
            } catch (AuthenticationException $e) {
                $event->setResponse($this->getResponseForbidden(
                      "Authentication exception ".$e->getMessage())
                      );
            } catch (\Exception $e) {
                var_dump($e);
            }
            
        } else {
            $event->setResponse($this->getResponseForbidden("no assertion parameter:listener"));
        }
    }

    
    private function getResponseForbidden($message = "") {
        $response = new Response($message);
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        return $response;
    }

}
