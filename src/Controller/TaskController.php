<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function index()
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findBy([], ['id' => 'DESC']);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    public function detail(Task $task)
    {
        return $this->render('task/detail.html.twig', [
            'task' => $task,
        ]);
    }

    public function creation(Request $request)
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->setUser($user);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('task_detail', ['id' => $task->getId()]);
        }

        return $this->render('task/creation.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function myTasks()
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        return $this->render('task/my-tasks.html.twig', [
            'tasks' => $user->getTasks(),
        ]);
    }

    public function edit(Request $request, Task $task)
    {
        $user = $this->getUser();
        if (!$user instanceof User || $user->getId() !== $task->getUser()?->getId()) {
            return $this->redirectToRoute('tasks');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('task_detail', ['id' => $task->getId()]);
        }

        return $this->render('task/creation.html.twig', [
            'edit' => true,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Task $task)
    {
        $user = $this->getUser();
        if (!$user instanceof User || $user->getId() !== $task->getUser()?->getId()) {
            return $this->redirectToRoute('tasks');
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('tasks');
    }
}
