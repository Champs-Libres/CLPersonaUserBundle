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
    
    
    public function setUp() {
        if (!file_exists(__DIR__.'/../Resources/user.php')) {
            throw new \Exception('The file '.__DIR__.'./../Resources/user.php'
                  . ' is not found. Did you copy user.php.dist ?');
        }
        
        require(__DIR__.'/../Resources/user.php');
        
        $this->personaEmail = $user;
        $this->personaPass = $pass;
    }
    
    public function testLoginExistingUserIsOK() {
        $client = static::createClient();
        $client->request('GET', '/persona/login', array(
           'assertion' => Helper::getPersonaAssertion(Helper::getPersonaTestUser())
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
