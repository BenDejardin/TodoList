<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class DefaultControllerTest extends WebTestCase
{
    // Test de redirection vers la page de connexion si l'utilisateur n'est pas connecté
    public function testRedirectionToLoginIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseRedirects('/login');
    }

    // Connexion d'un utilisateur avec un rôle ROLE_USER et vérification de la redirection vers la page d'accueil
    public function testHomepageWithUser(): void
    {
        $client = static::createClient();

        // Creer un connexion avec un utilisateur ROLE_USER
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);

        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(200);
        
    } 



}