<?php

namespace CL\PersonaUserBundle\Security\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use CL\PersonaUserBundle\Security\Token\PersonaUserToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use CL\PersonaUserBundle\Security\UserProvider\PersonaUserProviderInterface;
use CL\PersonaUserBundle\Services\Persona\PersonaService;
use CL\PersonaUserBundle\Security\Provider\PersonaIdNotExistingException;

/**
 * 
 *
 * @author julien.fastre@champs-libres.coop
 */
class PersonaAuthenticationProvider implements AuthenticationProviderInterface {
    
    /**
     *
     * @var \CL\PersonaUserBundle\Security\UserProvider\PersonaUserProviderInterface 
     */
    private $userProvider;
    
    /**
     *
     * @var \CL\PersonaUserBundle\Services\Persona\PersonaService;
     */
    private $personaService;
    
    public function __construct(UserProviderInterface $userProvider, 
        PersonaService $personaService) {
        $this->userProvider = $userProvider;
        $this->personaService = $personaService;
    }
    
    
    public function authenticate(TokenInterface $token) {
        if (! ($this->userProvider instanceof PersonaUserProviderInterface) ) {
            throw new AuthenticationException('The user provider provided '
                  . 'to '.  get_class($this).' is not an instance of '
                  . 'CL\PersonaUserBundle\Security\UserProvider\PersonaUser'
                  . 'ProviderInterface');
        }
        
        try {
            $res = $this->personaService->performAuthentication();
        
            $token->audience = $res['audience'];
            $token->email = $res['email'];
            $token->expire = $res['expires'];
            $token->issuer = $res['issuer'];
            
        } catch (Exception $ex) {
            throw new AuthenticationException('Persona authentication failed : '.
                    $ex->getMessage(), 0, $ex);
        }
        
        
        try {
            $user = $this->userProvider->loadUserByPersonaId($token->email);
        } catch (UsernameNotFoundException $ex) {
            throw new PersonaIdNotExistingException($res['email'], 'persona Id'
                  . ' still does not exist', 0, $ex);
        } catch (\Exception $ex) {
            throw new AuthenticationException('error loading user by persona',
                  0, $ex);
        }
        
        
        if ($user === NULL) {
            throw new AuthenticationException('user with persona token'.
                    $token->email.' not found');
        }
        
        if ($this->validate($token)) {
            
            $token->setUser($user);
            $token->setAuthenticated(TRUE);
//            $authToken = new PersonaUserToken($roles);
//            $authToken->setUser($user);
            return $token;
            
        }
        
        throw new AuthenticationException('The Persona authentication failed');
    }
    
    private function validate(PersonaUserToken $token) {
        return true; //TODO check expiration time
    }

    public function supports(TokenInterface $token) {
        return ($token instanceof PersonaUserToken);
    }

}
