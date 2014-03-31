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
    
    public function setUser($user) {
        parent::setUser($user);
        
        //add roles 
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = new Role($role);
            } elseif (!$role instanceof RoleInterface) {
                throw new \InvalidArgumentException(sprintf('$roles must be an array of strings, or RoleInterface instances, but got %s.', gettype($role)));
            }

            $this->roles[] = $role;
        }
    }

}
