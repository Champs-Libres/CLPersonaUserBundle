<?php

namespace CL\PersonaUserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use CL\PersonaUserBundle\Tests\LoginStaticHelper as Helper;

/**
 * Test the answer of an existing user.
 * 
 * You must provide username and pass of an existing user 
 * created by personatestuser.org in ./../Resources/user.php
 * 
 * This user MUST BE REGISTERED into your database.
 *
 * @author julien.fastre@champs-libres.coop
 */
class ExistingUserTest extends WebTestCase {
    
    protected $personaEmail;
    protected $personaPass;
    
    
    public static function getRegisteredTestUser() {
        $container = static::createClient()->getContainer();
        
        $personaEmail = $container->getParameter('cl_persona_user.testing.username');
        $personaPass = $container->getParameter('cl_persona_user.testing.password');
        
        return array('personaId' => $personaEmail,
           'personaPass' => $personaPass);
    }
    
    
    
    public function testLoginExistingUserIsOK() {
        $client = static::createClient();
        $client->request('GET', '/persona/login', array(
           'assertion' => Helper::getPersonaAssertion(static::getRegisteredTestUser())
        ));
        
        $response = $client->getResponse();
        
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        
        return $response;
    }
    
    
    /**
     * @depends testLoginExistingUserIsOK
     */
    public function testLoginEmailIsPresent($response) {
        $responseContent = json_decode($response->getContent());
        $this->assertNotEmpty($responseContent->email_login);
    }
    
    /**
     * 
     * @depends testLoginExistingUserIsOK
     */
    public function testLoginContentTypeJSON($response){
        $this->assertTrue($response
              ->headers->contains('Content-Type', 'application/json'));
    }
    
    
}
