<?php

namespace CL\PersonaUserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * user entity provided by PersonaUserInterface must implements
 * PersonaUserInterface, which extends UserInterface
 * 
 * @author julien.fastre@champs-libres.coop
 */
interface PersonaUserInterface extends UserInterface {
    
    public function getPersonaId();
    
}
