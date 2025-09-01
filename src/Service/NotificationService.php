<?php

namespace App\Service;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private EntityManagerInterface $em;
    private NotificationRepository $notificationRepository;

    public function __construct(EntityManagerInterface $em, NotificationRepository $notificationRepository)
    {
        $this->em = $em;
        $this->notificationRepository = $notificationRepository;
    }

    public function createNotification(string $message, int $userId): void
    {
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUserId($userId);

        $this->em->persist($notification);
        $this->em->flush();
    }

    public function getUserNotifications(int $userId): array
    {
        return $this->notificationRepository->findBy(
            ['userId' => $userId],
            ['createdAt' => 'DESC']
        );
    }
}
