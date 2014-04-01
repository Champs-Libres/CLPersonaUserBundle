<?php

namespace CL\PersonaUserBundle\Security\UserProvider;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * This class is used by the authentication provider to 
 * load user.
 * 
 * @author julien.fastre@champs-libres.coop
 */
interface PersonaUserProviderInterface extends UserProviderInterface {
    
    /**
     * Load user by the persona Id
     * 
     * @param string $personaId The persona id (email address)
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException if no users found
     */
    public function loadUserByPersonaId($personaId);
    
}
