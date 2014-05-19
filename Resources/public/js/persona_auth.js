/* jslint vars: true */
/*jslint indent: 3 */
/* global $ */
'use strict';
//Imported and adaptated from https://github.com/Proxiweb/PersonaBundle/blob/master/Resources/public/js/persona_auth.js

var signinLink = document.getElementById('signin');
var signoutLink = document.getElementById('signout');
var currentUser = null;


if (signinLink) {
   signinLink.onclick = function() { navigator.id.request(); };
}


if (signoutLink) {
   signoutLink.onclick = function() { navigator.id.logout(); };
}

if (window.localStorage.getItem('email_login')) {
   currentUser = window.localStorage.getItem('email_login');
}

console.log('current user is ');
console.log(currentUser);

navigator.id.watch({
   loggedInUser: currentUser,
   onlogin: function(assertion) {
      $.ajax({
         type: 'GET',
         url: personaLoginCheck,
         data: {assertion: assertion},
         success: function(res) {
            var r;
            console.log('login success');
            if (typeof res === 'string') {
               r = JSON.parse(res);
            } else {
               r = res;
            }
        
            window.localStorage.setItem('email_login',r.email_login);
            console.log(r);
            console.log(r.email_login);
            console.log(r.goTo);
            if ('goTo' in r ) {
               window.location.replace(r.goTo);
               return;
            }
        
            window.location.reload();
        
         },
         error: function(xhr, status, err) {
            console.log('login failure ' + err);
            navigator.id.logout();
         }
      });
   },
   onlogout: function() {
      $.ajax({
         type: 'GET',
         url: personaLogout,
         success: function() {
            currentUser = null;
            window.localStorage.removeItem('email_login');
            window.location.reload();
         },
         error: function(xhr, status, err) { alert('logout failure ' + err); }
      });
   }
});



