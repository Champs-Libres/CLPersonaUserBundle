<?php

namespace CL\PersonaUserBundle\Services\Persona;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class PersonaAuthenticationNoAssertionException extends \Exception {
    
    public function __construct($message = '', $code = 0, $previous = null) {  
        parent::__construct('no assertion parameter in URL', $code, $previous);
    }
    
    
}
