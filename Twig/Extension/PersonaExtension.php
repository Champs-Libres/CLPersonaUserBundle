<?php

/*
 * This file is part of the BGPersonaBundle package.
 * 
 * It was copied by Champs Libres and integrated into CLPersonaUserBundle.
 *
 * (c) bitgrave <http://bitgrave.github.com/>
 * (c) Champs Libres <http://www.champs-libres.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\PersonaUserBundle\Twig\Extension;

use BG\PersonaBundle\Templating\Helper\PersonaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class PersonaExtension extends \Twig_Extension implements ContainerAwareInterface {

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions() {
        return array(
           //'persona_initialize' => new \Twig_Function_Method($this, 'renderInitialize', array('is_safe' => array('html'))),
           'persona_login_button' => new \Twig_Function_Method($this, 'renderLoginButton', array('is_safe' => array('html'))),
           'persona_logout_url' => new \Twig_Function_Method($this, 'renderLogoutUrl', array('is_safe' => array('html'))),
           'persona_logout_button' => new \Twig_Function_Method($this, 'renderLogoutButton', array('is_safe' => array('html'))),
        );
    }

//    /**
//     * @see PersonaHelper::initialize()
//     */
//    public function renderInitialize($parameters = array(), $name = null)
//    {
//        return $this->container->get('bg_persona.helper')->initialize($parameters, $name ?: 'BGPersonaBundle::initialize.html.twig');
//    }

    /**
     * @see PersonaHelper::loginButton()
     */
    public function renderLoginButton($parameters = array(), $name = null) {
        return $this->container
              ->get('cl_persona_user.twig.persona_helper')
              ->loginButton(
                    $parameters, 
                    $name ? : 'CLPersonaUserBundle::userButton.html.twig');
    }
    
    public function renderLogoutButton($parameters = array(), $name = null) {
        return $this->container
              ->get('cl_persona_user.twig.persona_helper')
              ->logoutButton(
                    $parameters,
                    $name ? : 'CLPersonaUserBundle::userLogoutButton.html.twig');
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'persona';
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

}
