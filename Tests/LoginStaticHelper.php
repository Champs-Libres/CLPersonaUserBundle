<?php

namespace CL\PersonaUserBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Static methods to run login tests with persona
 *
 * @author julien.fastre@champs-libres.coop
 */
class LoginStaticHelper {
    
    public static $personaId;
    public static $personaPass;
    public static $authenticatedClient;
    
    const PERSONA_TEST_USER_GET_EMAIL_URL = "http://personatestuser.org/email";
    const PERSONA_TEST_USER_GET_ASSERTION = "http://personatestuser.org/assertion/";
    
    
    public static function getPersonaTestUser($createNew = false) {
        if ($createNew === TRUE OR static::$personaId === NULL) {
            $curl = curl_init(self::PERSONA_TEST_USER_GET_EMAIL_URL);
            //response as a string
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            echo("\n getting test user from personatestuser.org : wait a little bit\n");
            $response = curl_exec($curl);
            curl_close($curl);

            //handle the response
            $responseObject = json_decode($response);
            
            if (static::$personaId === NULL) {
                static::$personaId = $responseObject->email;
                static::$personaPass = $responseObject->pass;
            }
            
        }

        return array('personaId' => static::$personaId,
           'personaPass' => static::$personaPass);
    }
    
    
    
    public static function getPersonaAssertion(array $arrayPersonaIdPass) {
        
        
        $url = self::PERSONA_TEST_USER_GET_ASSERTION
              ."http%3A%2F%2Flocalhost/"
              .$arrayPersonaIdPass['personaId']."/"
              .$arrayPersonaIdPass['personaPass'];
        $curl = curl_init($url);

        //response as string
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        echo("\n getting an assertion from personatestuser.org : "
              . "wait a little bit\n");
        $response = curl_exec($curl);
        curl_close($curl);
        
        //handle response
        $responseObject = json_decode($response);

        return $responseObject->assertion;
    }
    
    public static function getAuthenticatedClient(array $arrayPersonaIdPass, 
          $createNew = false) {
        if ($createNew === true 
              OR static::$authenticatedClient[$arrayPersonaIdPass['personaId']] === NULL) {
            $client = static::createClient();
            $client->request('GET', '/persona/login', array(
               'assertion' => static::getPersonaAssertion(
                     static::getPersonaTestUser()
                     )
            ));
            
            //store the client in static resource if not exists
            if (static::$authenticatedClient[$arrayPersonaIdPass['personaId']] === NULL) {
                static::$authenticatedClient[$arrayPersonaIdPass['personaId']] = $client;
            }
            
            return $client;
        }
        
        return static::$authenticatedClient[$arrayPersonaIdPass['personaId']];
    }
    
}
