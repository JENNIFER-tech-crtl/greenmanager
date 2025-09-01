<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        Security $security,
        UserRepository $userRepo,
        TaskRepository $taskRepo,
        InvoiceRepository $invoiceRepo
    ): Response {
        $user = $security->getUser();

        if ($security->isGranted('ROLE_ADMIN')) {
            // Statistiques pour l'admin
            $totalUsers = $userRepo->count([]);
            $tasksTodo = $taskRepo->count(['status' => 'pending']);
            $tasksDoing = $taskRepo->count(['status' => 'in_progress']);
            $tasksDone = $taskRepo->count(['status' => 'done']);
            $invoicesPaid = $invoiceRepo->count(['status' => 'paid']);
            $invoicesUnpaid = $invoiceRepo->count(['status' => 'unpaid']);

            return $this->render('dashboard/dashboard_admin.html.twig', [
                'totalUsers' => $totalUsers,
                'tasksTodo' => $tasksTodo,
                'tasksDoing' => $tasksDoing,
                'tasksDone' => $tasksDone,
                'invoicesPaid' => $invoicesPaid,
                'invoicesUnpaid' => $invoicesUnpaid,
            ]);
        }

        // Dashboard utilisateur normal → ses propres tâches
        $userTasks = $taskRepo->findBy(['assignedTo' => $user]);

        return $this->render('dashboard/dashboard_user.html.twig', [
            'user' => $user,
            'tasks' => $userTasks,
        ]);
    }

    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    public function userDashboard(TaskRepository $taskRepo, Security $security): Response
    {
        $user = $security->getUser();
        $userTasks = $taskRepo->findBy(['assignedTo' => $user]);

        return $this->render('dashboard/dashboard_user.html.twig', [
            'user' => $user,
            'tasks' => $userTasks,
        ]);
    }
}
