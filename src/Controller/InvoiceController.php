<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Notification;
use App\Form\InvoiceType;
use App\Form\InvoiceClientType;
use App\Repository\InvoiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    // ------------------- Liste des factures ------------------- //
    #[Route('/', name: 'app_invoice_index')]
    public function index(InvoiceRepository $repo): Response
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $invoices = $repo->findAll();
        } else {
            $invoices = $repo->findBy(['assignedTo' => $user]);
        }

        return $this->render('invoice/index.html.twig', compact('invoices'));
    }

    // ------------------- Nouvelle facture interne ------------------- //
    #[Route('/new', name: 'app_invoice_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($invoice);
            $em->flush();

            // --- Création de notification interne ---
            if ($invoice->getAssignedTo()) {
                $notification = new Notification();
                $notification->setMessage("Une nouvelle facture (#".$invoice->getId().") vous a été assignée.");
                $notification->setUser($invoice->getAssignedTo());
                $em->persist($notification);
                $em->flush();
            }

            $this->addFlash('success', 'Facture interne ajoutée avec succès !');
            return $this->redirectToRoute('app_invoice_index');
        }

        return $this->render('invoice/new_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ------------------- Nouvelle facture client ------------------- //
    #[Route('/client/new', name: 'app_invoice_client_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newClient(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceClientType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($invoice);
            $em->flush();

            // --- Notification interne ---
            if ($invoice->getAssignedTo()) {
                $notification = new Notification();
                $notification->setMessage("Une nouvelle facture client (#".$invoice->getId().") vous a été assignée.");
                $notification->setUser($invoice->getAssignedTo());
                $em->persist($notification);
                $em->flush();
            }
// --- Création de notification interne ---
if ($invoice->getAssignedTo()) {
    $notification = new Notification();
    $notification->setMessage("Une nouvelle facture (#".$invoice->getId().") vous a été assignée.");
    $notification->setUser($invoice->getAssignedTo());
    $notification->setIsRead(false);
    $notification->setCreatedAt(new \DateTimeImmutable());

    $em->persist($notification);
    $em->flush();
}

            $this->addFlash('success', 'Facture client ajoutée avec succès !');
            return $this->redirectToRoute('app_invoice_index');
        }

        return $this->render('invoice/new_client.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ------------------- Édition facture ------------------- //
    #[Route('/{id}/edit', name: 'app_invoice_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Invoice $invoice, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // --- Notification interne si assignation ---
            if ($invoice->getAssignedTo()) {
                $notification = new Notification();
                $notification->setMessage("La facture (#".$invoice->getId().") vous a été assignée ou mise à jour.");
                $notification->setUser($invoice->getAssignedTo());
                $em->persist($notification);
                $em->flush();
            }
// --- Création de notification interne ---
if ($invoice->getAssignedTo()) {
    $notification = new Notification();
    $notification->setMessage("Une nouvelle facture (#".$invoice->getId().") vous a été assignée.");
    $notification->setUser($invoice->getAssignedTo());
    $notification->setIsRead(false);
    $notification->setCreatedAt(new \DateTimeImmutable());

    $em->persist($notification);
    $em->flush();
}

            $this->addFlash('success', 'Facture modifiée avec succès !');
            return $this->redirectToRoute('app_invoice_index');
        }

        return $this->render('invoice/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ------------------- Export PDF ------------------- //
    #[Route('/{id}/export/pdf', name: 'app_invoice_export_pdf')]
    public function exportPdf(Invoice $invoice): Response
    {
        $html = $this->renderView('invoice/pdf.html.twig', [
            'invoice' => $invoice,
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->stream('facture_'.$invoice->getId().'.pdf', ['Attachment' => true]));
    }

    // ------------------- Export Excel ------------------- //
    #[Route('/export/excel', name: 'app_invoice_export_excel')]
    #[IsGranted('ROLE_ADMIN')]
    public function exportExcel(InvoiceRepository $repo): Response
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'Client')
              ->setCellValue('C1', 'Montant')
              ->setCellValue('D1', 'Statut')
              ->setCellValue('E1', 'Créée le');

        $row = 2;
        foreach ($repo->findAll() as $invoice) {
            $sheet->setCellValue('A'.$row, $invoice->getId())
                  ->setCellValue('B'.$row, $invoice->getClient() ?? '')
                  ->setCellValue('C'.$row, $invoice->getAmount())
                  ->setCellValue('D'.$row, $invoice->getStatus())
                  ->setCellValue('E'.$row, $invoice->getCreatedAt()->format('d/m/Y H:i'));
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'factures.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}

