<?php

namespace CL\PersonaUserBundle\Services\Persona;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use CL\PersonaUserBundle\Services\Persona\PersonaAuthenticationNoAssertionException;

/**
 * Performs persona authentication
 *
 * @author julien.fastre@champs-libres.coop
 */
class PersonaService implements ContainerAwareInterface {
    
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;
    
    public $checkurl = 'https://verifier.login.persona.org/verify';

    

    /**
     * @return array the result of authentication
     * @throws PersonaAuthenticationFailedException if the authentication fail. The message get the reason.
     * @throws PersonaAuthenticationNoAssertionException if no assertion were found
     * @param string|null $assertion the assertion string. If not provided, the assertion is retrieved from the URL
     */
    public function performAuthentication($assertion = null) {
        $request = $this->container->get('request');
        
        
        if ($assertion === null) {
            $assertion = $request->query->get('assertion', null);
        }
        
        if ($assertion !== null) {

            $datas = http_build_query(
                  array(
                     'assertion' => $assertion, 
                     'audience' => $request->getSchemeAndHttpHost()));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->checkurl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            $res = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if ($res['status'] == 'okay') {
                return $res;
            } else {
                throw new PersonaAuthenticationFailedException($res['reason']);
            }
        } else {
            throw new PersonaAuthenticationNoAssertionException();
        }
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }

}
