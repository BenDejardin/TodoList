<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
     

    // Test ajout d'une tâche
    public function testCreate(): void
    {
        $client = static::createClient();

        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);

        $crawler = $client->request('GET', 'tasks/create');
        $this->assertResponseStatusCodeSame(200);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Nouvelle tâche';
        $form['task[content]'] = 'Contenu de la nouvelle tâche';
        $client->submit($form);

        // Test le getCreatedAt() de la tâche créée
        $task = $client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneByTitle('Nouvelle tâche');
        $this->assertNotNull($task->getCreatedAt());

        $this->assertResponseRedirects('/tasks');

        // Suppression de la tâche créée
        $taskId = $client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneByTitle('Nouvelle tâche')->getId();
        $client->request('GET', '/tasks/' . $taskId . '/delete');
        $this->assertResponseRedirects('/tasks');
    }

    public function testEdit(): void
    {
        $client = static::createClient();

        // Test redirection vers la page de connexion si l'utilisateur n'est pas connecté
        $client->request('GET', '/tasks/5/edit');
        $this->assertResponseRedirects('/login');
        
        // Connexion d'un utilisateur avec un rôle ROLE_USER
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);
        
        // Test modification d'une tâche qui appartient à l'utilisateur connecté
        $client->request('GET', '/tasks/5/edit');
        $this->assertResponseStatusCodeSame(200);
        
        // Test modification d'une tâche qui n'appartient pas à l'utilisateur connecté
        $client->request('GET', '/tasks/7/edit');
        $this->assertResponseRedirects('/tasks');
    }

    // Test Page Liste des tâches
    public function testList(): void
    {
        $client = static::createClient();

        // Test redirection vers la page de connexion si l'utilisateur n'est pas connecté
        $client->request('GET', '/tasks');
        $this->assertResponseRedirects('/login');

        // Connexion d'un utilisateur avec un rôle ROLE_USER
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);

        // Test de la page Liste des tâches
        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(200);
    }

    // Test Page Liste des tâches terminées
    public function testListDone(): void
    {
        $client = static::createClient();

        // Test redirection vers la page de connexion si l'utilisateur n'est pas connecté
        $client->request('GET', '/tasks-done');
        $this->assertResponseRedirects('/login');

        // Connexion d'un utilisateur avec un rôle ROLE_USER
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);

        // Test de la page Liste des tâches terminées
        $client->request('GET', '/tasks-done');
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de la méthode toggleTask dans TaskController
    public function testToggleTask(): void
    {
        $client = static::createClient();

        // Test redirection vers la page de connexion si l'utilisateur n'est pas connecté
        $client->request('GET', '/tasks/7/toggle');
        $this->assertResponseRedirects('/login');

        // Connexion d'un utilisateur avec un rôle ROLE_USER
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('user.123@gmail.com');
        $client->loginUser($user);

        // Test de la méthode toggleTask dans TaskController
        $client->request('GET', '/tasks/7/toggle');
        $this->assertResponseRedirects('/tasks');
    }

    // Test de la suppression d'une tâche
    public function testDeleteTask(): void
    {
        $client = static::createClient();

        // Test redirection vers la page de connexion si l'utilisateur n'est pas connecté
        $client->request('GET', '/tasks/7/delete');
        $this->assertResponseRedirects('/login');

    }
        
















    // Test de la méthode listDone dans TaskController
    // public function testListDone(): void
    // {
    //     $client = static::createClient();

    //     $client->request('GET', '/task-done');
    //     $this->assertResponseStatusCodeSame(200);
    // }

    // Test de la méthode create dans TaskController
    // public function testCreate(): void
    // {
    //     $client = static::createClient();

    //     $crawler = $client->request('GET', '/create');
    //     $this->assertResponseStatusCodeSame(200);

    //     $form = $crawler->selectButton('Ajouter')->form();
    //     $form['task[title]'] = 'Nouvelle tâche';
    //     $form['task[content]'] = 'Contenu de la nouvelle tâche';
    //     $client->submit($form);

    //     $this->assertResponseRedirects('/create');
    //     $client->followRedirect();
    //     $this->assertSelectorTextContains('h1', "Création d'une tâche");
    // }

    // // Test de la méthode edit dans TaskController
    // public function testEdit(): void
    // {
    //     $client = static::createClient();

    //     $crawler = $client->request('GET', '/tasks/1/edit');
    //     $this->assertResponseStatusCodeSame(200);

    //     $form = $crawler->selectButton('Modifier')->form();
    //     $form['task[title]'] = 'Tâche modifiée';
    //     $form['task[content]'] = 'Contenu de la tâche modifiée';
    //     $client->submit($form);

    //     $this->assertResponseRedirects('/task');
    //     $client->followRedirect();
    //     $this->assertSelectorTextContains('h1', 'Liste des tâches');
    // }

    // Test de la méthode toggleTask dans TaskController
    // public function testToggleTask(): void
    // {
    //     $client = static::createClient();

    //     $client->request('GET', '/tasks/1/toggle');
    //     $this->assertResponseRedirects('/task');
    //     $client->followRedirect();
    //     $this->assertSelectorTextContains('h1', 'Liste des tâches');
    // }

    // Test de la méthode deleteTask dans TaskController
    // public function testDeleteTask(): void
    // {
    //     $client = static::createClient();

    //     $client->request('GET', '/tasks/1/delete');
    //     $this->assertResponseRedirects('/task');
    //     $client->followRedirect();
    //     $this->assertSelectorTextContains('h1', 'Liste des tâches');
    // }
}