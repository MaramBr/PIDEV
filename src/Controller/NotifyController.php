<?php

namespace App\Controller;

use App\Entity\Notify;
use App\Form\NotifyType;
use App\Repository\NotifyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notify')]
class NotifyController extends AbstractController
{
    #[Route('/', name: 'app_notify_index', methods: ['GET'])]
    public function index(NotifyRepository $notifyRepository): Response
    {
        return $this->render('notify/index.html.twig', [
            'notifies' => $notifyRepository->findAll(),
        ]);
    }


    #[Route('/', name: 'notif', methods: ['GET'])]

    public function listenotify(Coaching $coaching): Response
    {
        $notify = $coaching->getNotifiess();
        return $this->render('notify/index.html.twig', [
            'coaching' => $coaching,
            'notify' => $notify,
        ]);
    }

    #[Route('/new', name: 'app_notify_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NotifyRepository $notifyRepository): Response
    {
        $notify = new Notify();
        $form = $this->createForm(NotifyType::class, $notify);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notifyRepository->save($notify, true);

            return $this->redirectToRoute('app_notify_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notify/new.html.twig', [
            'notify' => $notify,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notify_show', methods: ['GET'])]
    public function show(Notify $notify): Response
    {
        return $this->render('notify/show.html.twig', [
            'notify' => $notify,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_notify_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Notify $notify, NotifyRepository $notifyRepository): Response
    {
        $form = $this->createForm(NotifyType::class, $notify);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notifyRepository->save($notify, true);

            return $this->redirectToRoute('app_notify_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notify/edit.html.twig', [
            'notify' => $notify,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notify_delete', methods: ['POST'])]
    public function delete(Request $request, Notify $notify, NotifyRepository $notifyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notify->getId(), $request->request->get('_token'))) {
            $notifyRepository->remove($notify, true);
        }

        return $this->redirectToRoute('app_notify_index', [], Response::HTTP_SEE_OTHER);
    }
}
