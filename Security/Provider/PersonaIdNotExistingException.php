<?php

namespace CL\PersonaUserBundle\Security\Provider;



/**
 * Description of PersonaIdNotExistingException
 *
 * @author julien.fastre@champs-libres.coop
 */
class PersonaIdNotExistingException extends \Exception {
    
    public $personaId = NULL;
    
    public function __construct($personaId, $message, $code, $previous) {
        parent::__construct($message, $code, $previous);
        $this->personaId = $personaId;
    }
    
}
