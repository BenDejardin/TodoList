<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connectez vous');
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testLoginRedirectsToIndexWhenUserIsAuthenticated(): void
    {
        $client = static::createClient();
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user); 

        $client->request('GET', '/login');

        $this->assertResponseRedirects('/');
    }

    public function testLoginDisplaysErrorMessageWhenAuthenticationFails(): void
    {
        $client = static::createClient();
        $client->getContainer()->get('session')->clear();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form();
        $form['email'] = 'user.1234@gmail.com';
        $form['password'] = 'invalid_password';

        $client->submit($form);

        $crawler = $client->followRedirect();

        // Vérifier la présence du message d'erreur dans le nouveau contenu
        $this->assertSelectorExists('.alert-danger');
    }

}