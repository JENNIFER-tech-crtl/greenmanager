<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    public function list(NotificationRepository $repo): Response
    {
        $user = $this->getUser();
        $notifications = $repo->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC'] // plus récentes d’abord
        );

        return $this->render('notification/list.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notification/read/{id}', name: 'app_notification_read')]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): Response
    {
        if ($notification->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $notification->setIsRead(true);
        $em->flush();

        return $this->redirectToRoute('app_notifications');
    }
}
