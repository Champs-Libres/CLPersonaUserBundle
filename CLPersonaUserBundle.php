<?php

namespace CL\PersonaUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use CL\PersonaUserBundle\Security\Factory\PersonaFactory;

class CLPersonaUserBundle extends Bundle {
    
    const KEY_EMAIL_SESSION = 'email_login';
    const KEY_ROUTE_GOTO = 'goTo';

    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
        parent::build($container);


        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(
              new PersonaFactory()
        );
    }

}
