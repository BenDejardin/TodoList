<?php
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserControllerTest extends WebTestCase
{
    public function testList(): void
    {
        
        $client = static::createClient();
        $client->request('GET', '/users');
        
        // Test avec utilisateur non connecté
        $this->assertResponseRedirects('/login');

        // Test avec utilisateur connecté avec un rôle ROLE_USER
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);
        $client->request('GET', '/users');
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
        

        $client->request('GET', '/users');
        // Connexion d'un utilisateur avec un rôle ROLE_ADMIN
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('TOTO.123@yopmail.com');
        $client->loginUser($user);

        $client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testCreate(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users/create');
        // Test avec utilisateur non connecté
        $this->assertResponseRedirects('/login');

        // Test avec utilisateur connecté avec un rôle ROLE_USER
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);
        $client->request('GET', '/users/create');
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");

        // Connexion d'un utilisateur avec un rôle ROLE_ADMIN
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('TOTO.123@yopmail.com');
        $client->loginUser($user);

        $client->request('GET', '/users/create');
        $crawler = $client->request('GET', '/users/create');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', "Créer un utilisateur");
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'john doe';
        $form['user[email]'] = 'john.doe@example.com';
        $form['user[password][first]'] = 'password123';
        $form['user[password][second]'] = 'password123';
        $form['user[role]'] = 'ROLE_USER';

        $client->submit($form);

        $this->assertResponseRedirects('/users');
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-success', "Superbe ! L'utilisateur a bien été ajouté.");

        // Suppression de l'utilisateur créé
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('john.doe@example.com');
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
    }

    // Test modification d'un utilisateur
    public function testEdit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users/5/edit');
        // Test avec utilisateur non connecté
        $this->assertResponseRedirects('/login');

        // Test avec utilisateur connecté avec un rôle ROLE_USER
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);
        $client->request('GET', '/users/4/edit');
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");

        // Connexion d'un utilisateur avec un rôle ROLE_ADMIN
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('TOTO.123@yopmail.com');
        $client->loginUser($user);

        $client->request('GET', '/users/4/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Modifier test1");

        $crawler = $client->request('GET', '/users/4/edit');
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'test1';
        $form['user[email]'] = 'test.1234@gmail.com';
        $form['user[password][first]'] = 'password1232';
        $form['user[password][second]'] = 'password1232';
        $form['user[role]'] = 'ROLE_USER';

        $client->submit($form);

        $this->assertResponseRedirects('/users');
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-success', "Superbe ! L'utilisateur a bien été modifié");
    }


}