<?php

namespace CL\PersonaUserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use CL\PersonaUserBundle\Tests\LoginStaticHelper as Helper;

/**
 * Test wrong assertions
 *
 * @author julien.fastre@champs-libres.coop
 */
class WrongAssertionTest extends WebTestCase {
   
    public function testWrongAssertionReturn403() {
        $client = static::createClient();
        
        $client->request('GET', '/persona/login', array(
           'assertion' => '1234'
        ));
        
        $response = $client->getResponse();
        
        $this->assertEquals(403, $response->getStatusCode());
        
    }
    
    public function testOutdatedAssertionReturn403() {
        $client = static::createClient();
        
        $client->request('GET', '/persona/login', array(
           'assertion' => "eyJhbGciOiJSUzI1NiJ9.eyJwdWJsaWMta2V5Ijp7ImFsZ29yaXRobSI6IlJTIiwibiI6IjIzNzIyNDExMjA4NTQ3NDMxMTAwOTY2MTM4MTkwNjM3Nzk5NzMwNTkyOTQ0NTcyMzcxOTgwMTgxNTU0MTU1NzE4MTYxOTY5OTc2NjQ4MDAzNjM3Mjc0MjEzNzg1MzEyNjQwOTY5NTIzOTcwODE5NDYxNTYxMjI3NTUzNjM4MzMyMTgxODgzMjY2NDU5ODIzNjg1NDUzMDM2NTQyNzY2NDM3Mzk4MjA2OTM1NDkxNDc5MjM1NTM4MjMxOTEyMDQ4NjU0NzE1Mjc0NzY0ODUxNjYwNzUyNjI1OTcwNjAyMDM4OTQyNTQ2NDkwNjYyOTI5NTk1MTYxNjMyODc5OTQ5NTM3MDU4NDI3MTUxOTc2Njc1OTcyMDYyOTA2NTgyMDI4OTU3MTE5OTg4MTgxMTc5MTIwNTkzMDY4MzQ5NTUwMjEzOTI4OTE2Nzg2NDczNDE3MjM3NjE0OTU2ODQ5MDMwNTE2MjQ2Nzc0NDQ2MDI3ODQyNDY3Nzc2OTczNTAzODY3NjM2NjY4MDI2NzEyMTgwODYzNzk1MzQxNjkwNjkxNzA5ODk2ODA0OTc0Mjk4MTM0MTYzOTY0NDkwNjkyOTY4NTA2NDU0NTkwNjAxNTY5OTM0OTQyOTc1Mjc1NTg4MTExMjczOTU1MDk1NDIxMTQxNjE1Njk5NzU3OTI4NDE5OTI1OTkwMTkxMjc0OTk2NDQwMzY0MDE0NDY1OTUzNTcxMTQ1NTk1OTEzODMyODk2NzEwNTkwMDA4OTY1NTYyMjgyMTQ1MTgzNDQ2NjgyMjU0ODc2Njk2OTUzMzU2OTAzODA5MDA1MDY5NTY5IiwiZSI6IjY1NTM3In0sInByaW5jaXBhbCI6eyJlbWFpbCI6ImNhbmNlbDMzNDA5QHBlcnNvbmF0ZXN0dXNlci5vcmcifSwiaWF0IjoxMzk1MDAxNDg4MTAxLCJleHAiOjEzOTUwODc4ODgxMDEsImlzcyI6ImxvZ2luLnBlcnNvbmEub3JnIn0.CwTALfSXwfwM_zlxU73TeN-eBaVWpvCenSwMDFtyssu-crXyMLuWl1srR49T5lZsWpXbiWlLfefbJUv-5lTdkgVlxWg9btSewZ70J_npwkGEUiMxstlR7B7MxQE0UQYDGQ7efesoIJ-ivoY-NA9o-jUDlLZmnarm5TGU13Q9h4-CjeKNPxrdWrEPw2YdBLE6j28zXnenNaiEZCyYWh3xiu9-IFPE1L6H3shu1n-VqC5j8KSifbUBlQrmugT60e9mG6AMBE8xXcoxw5ycjcjitA0y0mEJpbvslqNy42uSycGw3LuXkJFhloDiBvwpA2QidGSx_kjcHKIzqGIzfGM3tg~eyJhbGciOiJSUzI1NiJ9.eyJleHAiOjEzOTUwMDUwODcwMDAsImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3QifQ.SG14qMyWjuKe4NAEptCSHs99OdqJIxVmtx7UFPgkE17i58DwVnTYA8cRKh5QS9sjsoGUEFkeUtbK53_T3JBqj6sFl2vhiCUiCExIYIbYL1fdEK83LAYLiBJlOWySO1eCJZu7oSCXvry3nGWFQlPHQ4vdVEsQbrWvG4vzgFVFQG_5qe40cgj4NUBtlU24rxAsmEo6E7J_0TU50CD7A-nJLdJfKq7sD3yne_zSOeJVFrjtR_wE-nWyw1S3ub173RLmECb7Ps281KyVgVuYL6gW-2sqJvgSy_YqmA6xygBQROGX8JN1NEL1qlKQLqm6RnSFuw_djftUYsYJqlCkr4lY5A"

        ));
        
        $response = $client->getResponse();
        
        $this->assertEquals(403, $response->getStatusCode());
        
    }
    
    
}
