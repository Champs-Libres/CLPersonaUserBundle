<?php

namespace CL\PersonaUserBundle\Security\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\Role;


class PersonaUserToken extends AbstractToken {
    
    public $email;
    public $audience;
    public $expire;
    public $issuer;
    
    public function __construct(array $roles = array()) {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials() {
        return $this->getUser()->getCredentials();
    }
    
}
