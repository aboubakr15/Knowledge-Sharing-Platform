<?php

namespace App\Services;

use App\Models\Notification;
use App\Utils\DBConnection;
use PDO;

class NotificationService implements Service
{
    private $db;

    public function __construct()
    {
        $this->db = DBConnection::getConnection();
    }

    public function create(object $data)
    {
        try {
            $is_read = $data->getIsRead() ? 1 : 0;

            $query = "INSERT INTO Notifications (user_id, targeted_user_id, notification_type, source_id, is_read, created_at) VALUES (:user_id,:targeted_user_id ,:notification_type, :source_id, :is_read, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'user_id' => $data->getUserId(),
                'targeted_user_id' => $data->getTargetedUserId(),
                'notification_type' => $data->getNotificationType(),
                'source_id' => $data->getSourceId(),
                'is_read' => $is_read,
            ]);

        } catch (\Exception $ex) {
            echo "<pre>";
            var_dump($ex);
            echo "</pre>";
        }
        return $this->db->lastInsertId();
    }

    public function getById(int $id)
    {
        $query = "SELECT * FROM Notifications WHERE notification_id = :notification_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['notification_id' => $id]);
        $notificationData = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Notification(
            $notificationData['notification_id'],
            $notificationData['user_id'],
            $notificationData['targeted_user_id'],
            $notificationData['notification_type'],
            $notificationData['source_id'],
            $notificationData['is_read'],
            $notificationData['created_at']
        );
    }


    public function getAll()
    {
        $query = "SELECT * FROM Notifications";
        $stmt = $this->db->query($query);
        $notificationDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $notifications = [];
        foreach ($notificationDataArray as $notificationData) {
            $notifications[] = new Notification(
                $notificationData['notification_id'],
                $notificationData['user_id'],
                $notificationData['targeted_user_id'],
                $notificationData['notification_type'],
                $notificationData['source_id'],
                $notificationData['is_read'],
                $notificationData['created_at']
            );
        }
        return $notifications;
    }

    public function getLimit(int $userId, int $limit): array
    {
        try {
            $query = "SELECT * FROM Notifications WHERE targeted_user_id = :targeted_user_id AND is_read = 0 ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':targeted_user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $notifications = [];
            while ($notificationData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $notifications[] = new Notification(
                    $notificationData['notification_id'],
                    $notificationData['user_id'],
                    $notificationData['targeted_user_id'],
                    $notificationData['notification_type'],
                    $notificationData['source_id'],
                    $notificationData['is_read'],
                    $notificationData['created_at']
                );
            }
            return $notifications;
        } catch (\Exception $ex) {
            var_dump($ex);
            return [];
        }
    }


    public function update(int $id, object $data)
    {
        $query = "UPDATE Notifications SET user_id = :user_id, notification_type = :notification_type, source_id = :source_id, is_read = :is_read, created_at = :created_at WHERE notification_id = :notification_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'notification_id' => $id,
            'user_id' => $data->getUserId(),
            'notification_type' => $data->getNotificationType(),
            'source_id' => $data->getSourceId(),
            'is_read' => $data->getIsRead(),
            'created_at' => $data->getCreatedAt()
        ]);
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM Notifications WHERE notification_id = :notification_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['notification_id' => $id]);
    }

    public function markAllAsRead(int $userId)
    {
        try {
            $query = "UPDATE Notifications SET is_read = 1 WHERE targeted_user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userId]);
        } catch (\Exception $ex) {
            var_dump($ex);
        }
    }

}
