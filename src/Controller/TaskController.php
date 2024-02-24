<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Services\User as ServicesUser;
use App\User\User as UserUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[Route('/task', name: 'task_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $tasks = $entityManager->getRepository(Task::class)->getTaskByUserIdAndNoDone($user);

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/task-done', name: 'task_list_done')]
    public function listDone(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $tasks = $entityManager->getRepository(Task::class)->getTaskByUserIdAndDone($user);

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $task->setUserId($user);
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->toggle(false);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $task = $entityManager->getRepository(Task::class)->find($id);
        // dd($this->getUser());
        if($task->getUserId() != $this->getUser()) return $this->redirectToRoute('task_list'); 

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(int $id, EntityManagerInterface $entityManager): Response
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        if($task->getUserId() != $this->getUser()) return $this->redirectToRoute('task_list'); 
        $task->toggle(!$task->isDone());
        $entityManager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        // Le statue de la Task a changer mais nous devons rester sur la page ou celle ci etais précédament
        return $task->isDone() == true ? $this->redirectToRoute('task_list'): $this->redirectToRoute('task_list-done'); 
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTask(int $id, EntityManagerInterface $entityManager): Response
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        if($task->getUserId() != $this->getUser()) return $this->redirectToRoute('task_list'); 

        if (!$task) {
            // Gérer le cas où la tâche n'est pas trouvée
            throw $this->createNotFoundException('La tâche n\'existe pas.');
        }

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
