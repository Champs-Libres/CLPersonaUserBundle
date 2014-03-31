CLPersonaUserBundle
===================

This bundle brigns authentication with the amazing [Persona system](https://www.mozilla.org/en-US/persona/) into symfony projects.

This bundle may be used by itself: you do not need to use another bundle. 

You may create users "on the fly": they will simply click the "login or register" button and will have (if you need it) a form.

Installation
============

Download with composer
----------------------

With Composer

TODO

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

Create an UserProvider
-----------------------

You must create an user provider, which is an implementation of 
`CL\PersonaUserBundle\Security\UserProvider\PersonaUserProviderInterface`.

`CL\PersonaUserBundle\Security\UserProvider\PersonaUserProviderInterface` extends 
`Symfony\Component\Security\Core\User\UserProviderInterface` and add one method : 

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

Register this user provider into your project :

```yml
#app/config/security.yml

security:
    providers:
        persona_provider:
            id: my_persona_provider_service

```
Define the service :

```yml
#YourBundle/Resource/config/services.yml

services:
    my_person_provider_service:
        class: PATH/TO/YOUR/PersonaUserProviderImplementation
        #your definition and dependencies
```

Define sections into security.yml
----------------------------------

```yml
#app/config/security.yml

security:
    firewalls:
        persona:
            pattern: ^/persona #your path must include /persona path !
            provider: my_persona_provider_service #add your provider
            persona: true #required: this allow persona authentication methods
            context: my_context #you may adapt to your context if you need multiple firewalls


        #optional: to protect other parts of your application
        my_other_part:
            pattern: ^/my_part
            context: my_context #this will link to the context of persona, below.

```
            
Configure the bundle
---------------------


You must add the required option to your `config.yml` file : 

```
cl_persona_user:
    route_not_existing_user: 'my_bundle.register_user'
```

This route must, of course, exists, and should not require any parameter. See below.



Add a method to register new users
----------------------------------

The route you add to the configuration must deals with users not in the database.

The persona id is stored in the session and is available within your controllers.

If you need this, you may login the user directly after user creation, using the 
service 'cl_persona_user.manual_login' like this : `$container->get('cl_persona_user.manual_login')
->authenticate($user)`.

For instance :

The route : 

```yml
my_bundle.register_user:
    pattern: /register
    defaults: { _controller: MyBundle:Default:registerForm }
```

The controller : 

```php 

class DefaultController extends Controller {

    public function registerFormAction(Request $request) {
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

Every time you offers the possibility to login with Persona, you must manually add the following scripts :

```html
<!DOCTYPE html>
<html>
    <head>
        <!-- for the layout of buttons -->
        {% stylesheets '@CLPersonaUserBundle/Resources/public/css/*' filter='cssrewrite' %}
        <link rel="stylesheet" href="{{ asset_url }}" />
        {% endstylesheets %}
        
        <!-- you must add jquery somwhere in the page, or use the 1.11 version provided with the script -->
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
            var personaLogout = '{{ path('cl_persona_user.logout') }}';
        </script>
        
        <script src="https://login.persona.org/include.js"></script>
        
        {% javascripts '@CLPersonaUserBundle/Resources/public/js/persona_auth.js' %}
        <script type="text/javascript" src="{{ asset_url }}"></script>
        {% endjavascripts %}

```


Test
-----

TODO


