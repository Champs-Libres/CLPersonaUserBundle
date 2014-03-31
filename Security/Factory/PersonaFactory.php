<?php

namespace CL\PersonaUserBundle\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;


/**
 * 
 *
 * @author julien.fastre@champs-libres.coop
 */
class PersonaFactory implements SecurityFactoryInterface {
    

    
    
    public function addConfiguration(NodeDefinition $builder) {
        
    }

    public function create(ContainerBuilder $container, 
          $id, $config, $userProvider, $defaultEntryPoint) {
        

        $providerId = 'cl_persona_user.security.authentication.'
          . 'provider.persona.'.$id;
        $container
            ->setDefinition($providerId, 
                  new DefinitionDecorator('cl_persona_user.security.'
                        . 'authentication.provider.persona'))
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'cl_persona_user.security.authentication.'
              . 'listener.persona.'.$id;
        $container->setDefinition($listenerId, 
              new DefinitionDecorator(
                    'cl_persona_user.security.persona.authentication.'
                    . 'listener'
                    ));

        return array($providerId, $listenerId, $defaultEntryPoint);

    }

    public function getKey() {
        return 'persona';
    }

    public function getPosition() {
        return 'http';
    }

}
