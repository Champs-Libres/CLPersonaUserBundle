<?php

namespace CL\PersonaUserBundle\Services\Persona;

/**
 * 
 *
 * @author julien.fastre@champs-libres.coop
 */
class PersonaAuthenticationFailedException extends \Exception {
    
    public function __construct($message = null, $code = 0 , $previous = null) {
        
        if ($message === null) {
            $message = "The persona authentication failed";
        }
        
        parent::__construct($message, $code, $previous);
    }
    
}
