<?php

namespace CL\PersonaUserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cl_persona_user');
        
        $rootNode
                ->children()
                    ->scalarNode('route_not_existing_user')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('testing')
                        ->children()
                            ->scalarNode('username')->defaultNull()->end()
                            ->scalarNode('password')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
