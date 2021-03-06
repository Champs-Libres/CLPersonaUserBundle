<?php

namespace CL\PersonaUserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use CL\PersonaUserBundle\Security\Factory\PersonaFactory;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CLPersonaUserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('cl_persona_user.route_not_existing_user', 
              $config['route_not_existing_user']);
        
        //for tests, set parameters from config.yml
        if (isset($config['testing']['username'])) {
            $container->setParameter('cl_persona_user.testing.username', 
                  $config['testing']['username']);
        } else {
            $container->setParameter('cl_persona_user.testing.username', null);
        }
        
        if (isset($config['testing']['password'])) {
            $container->setParameter('cl_persona_user.testing.password', 
                  $config['testing']['password']);
        } else {
            $container->setParameter('cl_persona_user.testing.password', null);

        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
