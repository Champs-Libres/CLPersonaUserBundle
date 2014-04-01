CLPersonaUserBundle
===================

This bundle brigns authentication with the amazing [Persona system](https://www.mozilla.org/en-US/persona/) into symfony projects.

This bundle may be used alone: you do not need to use another bundle. 

You may create users "on the fly": they will simply click the "sign in with persona" button and the magic is done !

Installation
============

Download with composer
----------------------

Add the following line to your `composer.json` file : 

```json

{
   "requires" : [
        #...
        "champs-libres/persona-user-bundle" : "*@dev"
    ]

}

```

And, then, run the `php composer.phar install` command.

Enable the bundle
------------------

In your `app/AppKernel.php`, add the following lines :

```php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new CL\PersonaUserBundle\CLPersonaUserBundle(),
            // ...
        );

            //...

        return $bundles;
    }
```

Add the bundle to assetic and enable translation:

```yml

framework:
    translator:      { fallback: "%locale%" }


assetic:
    #....
    bundles:        ['CLPersonaUserBundle' ]
```

Configure the bundle
---------------------

Add the required routes to your bundle :

```yml
#app/routing.yml

CLPersonaUserBundle:
        resource: "@CLPersonaUserBundle/Resources/config/routing.yml"
        prefix:   /

```

You must also add the required options to your `config.yml` file : 

```
cl_persona_user:
    route_not_existing_user: 'my_bundle.register_user'
```

This route must, of course, exists, and should not require any parameter. See below.

Create an User class
---------------------

Implements both  `CL\PersonaUserBundle\Entity\PersonaUserInterface` and `Symfony\Component\Security\Core\User\UserInterface` (or his subclass) on you user class.

If you do not use another way of login, you may leave blank the function needed for 'symfony' user interfaces. You may, then, use your user interface with another user bundle.

Example of an User class: 

```php

<?php

namespace CL\Cyclabilite\UserBundle\Entity;

use CL\PersonaUserBundle\Entity\PersonaUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of User
 *
 * @author julien
 */
class User implements PersonaUserInterface, \Serializable, UserInterface {
    
    private $email = '';
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $label = '';

    
    /**
     * @var string[]
     */
    private $roles = array('ROLE_USER');
    
    
    public function eraseCredentials() {
        
    }

    public function getPassword() {
        return '';
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getSalt() {
        return '';
    }

    public function getUsername() {
        return $this->email;
    }

    public function getPersonaId() {
        return $this->getEmail();
    }

}



```


Create an UserProvider
-----------------------

You must create an user provider, which is an implementation of 
`CL\PersonaUserBundle\Security\UserProvider\PersonaUserProviderInterface`.

`CL\PersonaUserBundle\Security\UserProvider\PersonaUserProviderInterface` extends 
`Symfony\Component\Security\Core\User\UserProviderInterface` and add one method. Here is how the interface is defined :

```php

interface PersonaUserProviderInterface extends UserProviderInterface {
    
    /**
     * Load user by the persona Id
     * 
     * @param string $personaId The persona id (email address)
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function loadUserByPersonaId($personaId);
    
}

```

Here is an example of an UserProvider implementation:

``php

namespace MyBundle\Security\Provider;

use Doctrine\ORM\EntityManagerInterface;
use CL\PersonaUserBundle\Security\UserProvider\PersonaUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use CL\PersonaUserBundle\Security\Provider\PersonaIdNotExistingException;

/**
 * Example of an UserProvider Implementation
 *
 * 
 */
class UserProvider implements PersonaUserProviderInterface {
    
    private $em;
    
    
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    
    
    
    public function loadUserByPersonaId($personaId) {
        $user = $this->em->getRepository('MyBundle:User')
                ->findOneBy(array('personaId' => $personaId));
        
        if ($user === null) {
            throw new UsernameNotFoundException($personaId);
        }
        
        
        return $user;
    }

    public function loadUserByUsername($username) {
        $user = $this->em->getRepository('MyBundle:User')
                ->findBy(array('username' => $username));
        
        if ($user === null) {
            throw new UsernameNotFoundException("The user with username "
                    . $username . " is not found");
        }
        
        
        return $user;
    }

    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user) {
        if ( ! $this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException('class '.get_class($user).
                  " is not supported by ".get_class($this));
        }
        
        try {
            return $this->em->getRepository('MyBundle:User')
                  ->find($user->getId());
        } catch (\Exception $e) {
            throw new \Exception("problem on refreshing user ", 0, $e);
        }
    }

    public function supportsClass($class) {
        return $class === 'MyBundle\Entity\User';
    }

}


```

Define the class as a service :

```yml
#YourBundle/Resource/config/services.yml

services:
    my_person_provider_service:
        class: PATH/TO/YOUR/PersonaUserProviderImplementation
        #your definition and dependencies
```

Register this user provider into your project :

```yml
#app/config/security.yml

security:
    providers:
        persona_provider:
            id: my_persona_provider_service

```


Define sections into security.yml
----------------------------------

```yml
#app/config/security.yml

security:
    firewalls:
        persona:
            pattern: ^/persona #your path must  at least include /persona path !
            provider: my_persona_provider_service #add your provider
            persona: true #required: this allow persona authentication methods
            context: my_context #you may adapt to your context if you need multiple firewalls


        #optional: to protect other parts of your application
        my_other_part:
            pattern: ^/my_part
            context: my_context #this will link to the context of persona.
            logout:
                path: /persona/logout #you may replace this
                target: /persona/login #you may replace this

```
            
Add a method to register new users
----------------------------------

When a user login for the first time, you must create an account and profile in the database, or block the registration.

Every time a not-existing-user connects, the javascript will redirect to the URL defined in `cl_persona_user.route_not_existing_user`, in `config.yml`. It is your responsability to do whatever you want with new users : display a new form, directly register the user, ...

The persona id's new user is stored in the session and is available within your controllers.

A lot of people will registre new user and persist them in the database. If you need this, you may login the user directly after user creation, using the service `cl_persona_user.manual_login` like this : `$container->get('cl_persona_user.manual_login')->authenticate($user)`.

For instance :

The route : 

```yml
my_bundle.register_user:
    pattern: /register
    defaults: { _controller: MyBundle:Default:register }
```

The controller : 

```php 

class DefaultController extends Controller {

    /*
     * Here we will display a form to ask some information, 
     * and then deals with this form
     */
    public function registerAction(Request $request) {
        $emailRecorded = $this->get('session')
              ->get(CLPersonaUserBundle::KEY_EMAIL_SESSION, null);
        
        if ($emailRecorded === NULL) {
            $response = new Response("You must authenticate with persona first!");
            $response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
            return $response;
        }
        
        $user = new User();
        $user->setUsername($emailRecorded); #username must match persona Id!
        
        $form = $this->createForm(new UserType(), $user);

        if ($request->getMethod() === 'POST') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                
                #Authenticate the user immediatly
                $this->get('cl_persona_user.manual_login')
                      ->authenticate($user);

                return $this->redirect(
                      $this->generateUrl('my_bundle.registration_confirmed'));
            } 
        }

        return $this->render('CLCyclabiliteUserBundle:Register:form.html.twig', array(
                    'form' => $form->createView()
                        )
        );
    }

}

```

Twig templates and javascripts files
====================================

You may use the `/persona/login` route for your bundle, or add a "login with persona" button on each page.

Every time you offers the possibility to login with Persona, you must manually add the following scripts :

```html
<!DOCTYPE html>
<html>
    <head>
        <!-- for the layout of buttons, not really necessary -->
        {% stylesheets '@CLPersonaUserBundle/Resources/public/css/*' filter='cssrewrite' %}
        <link rel="stylesheet" href="{{ asset_url }}" />
        {% endstylesheets %}
        
        <!-- you must add jquery somwhere in the page, or use the 1.11 version provided with the script. Do not hesitate to replace it. -->
        {% javascripts '@CLPersonaUserBundle/Resources/public/js/jquery-1.11.0.js' %}
        <script type="text/javascript" src="{{ asset_url }}"></script>
        {% endjavascripts %}
     </head>

     <body>

     <!-- the login button -->
     <p>{{ persona_login_button() }}</p>

     <!-- for logout, use {{ persona_logout_button }} -->


     <!-- YOUR CONTENT -->


     <!-- for persona login -->
        

        <!-- THOSE VARIABLES ARE NEEDED FOR THE ADAPTATION OF THE js SCRIPT BELOW -->
        <script type="text/javascript">
            var personaLoginCheck = '{{ path('cl_persona_user.login_check') }}';
            var personaLogout = '{{ path('cl_persona_user.logout') }}'; //replace with another logout route if needed
        </script>
        
     <!-- needed by persona ! -->
        <script src="https://login.persona.org/include.js"></script>
     <!-- the script we develop. This script will reload the page after login. You may create your own -->  
        {% javascripts '@CLPersonaUserBundle/Resources/public/js/persona_auth.js' %}
        <script type="text/javascript" src="{{ asset_url }}"></script>
        {% endjavascripts %}

```


Test
-----

You may test user in your application.


