<?php

namespace CL\PersonaUserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use CL\PersonaUserBundle\Tests\LoginStaticHelper as Helper;

/**
 * Test the creation of a User
 *
 * @author julien.fastre@champs-libres.coop
 */
class UserCreationTest extends WebTestCase {
    
    /**
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;
    
    private $configuredRoute;
    
    
    public function setUp(){
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->router = static::$kernel->getContainer()
            ->get('router')
        ;
        $this->configuredRoute = static::$kernel->getContainer()
                ->getParameter('cl_persona_user.route_not_existing_user');
    }
    
    
    public function testPersonaTestUserIdIsOk() {
        $arrayPersonaIdPass = Helper::getPersonaTestUser();
        
        $this->assertNotEmpty($arrayPersonaIdPass['personaId']);
    }
    
    public function testPersonaTestUserPassIsOk(){
        $arrayPersonaIdPass = Helper::getPersonaTestUser();
        
        $this->assertNotEmpty($arrayPersonaIdPass['personaPass']);
    }
    

    
    public function testLoginCreationIsOK() {
        $client = static::createClient();
        $client->request('GET', '/persona/login', array(
           'assertion' => Helper::getPersonaAssertion(Helper::getPersonaTestUser())
        ));
        
        $response = $client->getResponse();
        
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        
        return $response;
    }
    
    /**
     * 
     * @depends testLoginCreationIsOK
     */
    public function testGoToIsPresent($response) {
        $responseContent = json_decode($response->getContent());
        $this->assertNotEmpty($responseContent->goTo);
        
        return $responseContent->goTo;
    }
    
    /**
     * 
     * @depends testGoToIsPresent
     */
    public function testGoToEqualConfiguration($goToUrl){
        $expectedUrl = $this->router->generate($this->configuredRoute, array(),
                true);
        
        $this->assertEquals($goToUrl, $expectedUrl);
    }
    
    /**
     * @depends testLoginCreationIsOK
     */
    public function testLoginEmailIsPresent($response) {
        $responseContent = json_decode($response->getContent());
        $this->assertNotEmpty($responseContent->email_login);
    }
    
    /**
     * 
     * @depends testLoginCreationIsOK
     */
    public function testLoginContentTypeJSON($response){
        $this->assertTrue($response
              ->headers->contains('Content-Type', 'application/json'));
    }
    
}
