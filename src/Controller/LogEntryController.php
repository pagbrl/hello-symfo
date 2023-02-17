<?php

namespace App\Controller;

use App\Entity\LogEntry;
use App\Form\LogEntryType;
use App\Repository\LogEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class LogEntryController extends AbstractController
{
    #[Route('/', name: 'app_log_entry_index', methods: ['GET'])]
    public function index(LogEntryRepository $logEntryRepository): Response
    {
        return $this->render('log_entry/index.html.twig', [
            'log_entries' => $logEntryRepository->findAll(),
        ]);
    }

    #[Route('/up', name: 'app_log_healthcheck', methods: ['GET'])]
    public function healthcheck(LogEntryRepository $logEntryRepository): Response
    {
        return $this->json('OK', 200);
    }

    #[Route('/new', name: 'app_log_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LogEntryRepository $logEntryRepository): Response
    {
        $logEntry = new LogEntry();
        $form = $this->createForm(LogEntryType::class, $logEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logEntryRepository->save($logEntry, true);

            return $this->redirectToRoute('app_log_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('log_entry/new.html.twig', [
            'log_entry' => $logEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_log_entry_show', methods: ['GET'])]
    public function show(LogEntry $logEntry): Response
    {
        return $this->render('log_entry/show.html.twig', [
            'log_entry' => $logEntry,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_log_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LogEntry $logEntry, LogEntryRepository $logEntryRepository): Response
    {
        $form = $this->createForm(LogEntryType::class, $logEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logEntryRepository->save($logEntry, true);

            return $this->redirectToRoute('app_log_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('log_entry/edit.html.twig', [
            'log_entry' => $logEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_log_entry_delete', methods: ['POST'])]
    public function delete(Request $request, LogEntry $logEntry, LogEntryRepository $logEntryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $logEntry->getId(), $request->request->get('_token'))) {
            $logEntryRepository->remove($logEntry, true);
        }

        return $this->redirectToRoute('app_log_entry_index', [], Response::HTTP_SEE_OTHER);
    }
}
