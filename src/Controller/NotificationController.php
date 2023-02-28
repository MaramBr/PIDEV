<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'app_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): Response
    {
        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationRepository->findAll(),
        ]);
    }

    #[Route('/afficher', name: 'afficher', methods: ['GET'])]
    public function afficher(NotificationRepository $notificationRepository): Response
    {
        return $this->render('notification/affich.html.twig', [
            'notifications' => $notificationRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_notification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NotificationRepository $notificationRepository): Response
    {
        $notification = new Notification();
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notificationRepository->save($notification, true);

            return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notification/new.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notification_show', methods: ['GET'])]
    public function show(Notification $notification): Response
    {
        return $this->render('notification/show.html.twig', [
            'notification' => $notification,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_notification_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Notification $notification, NotificationRepository $notificationRepository): Response
    {
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notificationRepository->save($notification, true);

            return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notification/edit.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notification_delete', methods: ['POST'])]
    public function delete(Request $request, Notification $notification, NotificationRepository $notificationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notification->getId(), $request->request->get('_token'))) {
            $notificationRepository->remove($notification, true);
        }

        return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
    }

 #[Route('/testing', name: 'notification_unread')]
    public function getUnreadNotifications()
    {
    $notifications = $this->getDoctrine()
        ->getRepository(Notification::class)
        ->findBy(['isRead' => false], ['createdAt' => 'DESC']);

    return $this->render('notification/unread.html.twig', [
        'notifications' => $notifications
    ]);
}


#[Route('/{id}/update', name: 'app_notification_update', methods: ['GET', 'POST'])]

public function update(Notification $notification, Request $request)
{
    $form = $this->createForm(NotificationType::class, $notification);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $notification->setIsRead($request->request->get('isRead', false));
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_index');
    }

    return $this->render('notification/show.html.twig', [
        'notification' => $notification,
        'form' => $form->createView(),
    ]);
}
}
