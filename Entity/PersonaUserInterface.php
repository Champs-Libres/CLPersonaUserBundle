<?php

namespace CL\PersonaUserBundle\Entity;



/**
 * user entity provided by PersonaUserInterface must implements
 * PersonaUserInterface, 
 * 
 * @author julien.fastre@champs-libres.coop
 */
interface PersonaUserInterface {
    
    public function getPersonaId();
    
}
