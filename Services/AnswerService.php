<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Notification;
use App\Utils\DBConnection;
use Exception;
use PDO;

class AnswerService implements Service
{
    private $db;

    private $userService;
    private $notificationService;

    public function __construct()
    {
        $this->db = DBConnection::getConnection();
        $this->userService = new UserService();
        $this->notificationService = new NotificationService();
    }

    public function create(object $data)
    {
        $query = "INSERT INTO Answers (user_id, question_id, body, created_at, reputations) VALUES (:user_id, :question_id, :body, :created_at, :reputations)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'user_id' => $data->user_id, // Access user_id directly
            'question_id' => $data->question_id, // Access question_id directly
            'body' => $data->body,
            'created_at' => $data->created_at,
            'reputations' => $data->reputations
        ]);

        try {
            $targetedUserId = (new QuestionService())->getById($data->question_id)->getUserId();
            $notification = new Notification(
                0,
                $data->user_id, // Access user_id directly
                $targetedUserId,
                'answer',
                $data->question_id, // Access question_id directly
                false
            );

            $this->notificationService->create(
                $notification
            );

            $this->updateUserReputationAndBadges($data->user_id);

        } catch (Exception $ex) {
            var_dump($ex);
        }

        return $this->db->lastInsertId();
    }


    private function updateUserReputationAndBadges($userId)
    {
        try {
            $user = $this->userService->getById($userId);
            $updatedReputation = $user->getReputations() + 10;

            // Determine the badge based on the updated reputation
            $badgeId = null;
            if ($updatedReputation >= 100 && $updatedReputation < 500) {
                $badgeId = 1;
            } elseif ($updatedReputation >= 500 && $updatedReputation < 1000) {
                $badgeId = 2;
            } elseif ($updatedReputation >= 1000) {
                $badgeId = 3;
            }

            // Check if the user already has the badge
            if ($badgeId !== null) {
                $existingBadge = $this->db->prepare("SELECT * FROM User_Badges WHERE user_id = :user_id AND badge_id = :badge_id");
                $existingBadge->execute(['user_id' => $userId, 'badge_id' => $badgeId]);
                $badgeExists = $existingBadge->fetch();

                // If the user doesn't have the badge, assign it
                if (!$badgeExists) {
                    $insertBadge = $this->db->prepare("INSERT INTO User_Badges (user_id, badge_id) VALUES (:user_id, :badge_id)");
                    $insertBadge->execute(['user_id' => $userId, 'badge_id' => $badgeId]);
                }
            }

            // Update user's reputation
            (new UserService())->increaseReputations($userId, 10);
        } catch (Exception $ex) {
            // Handle the exception
            var_dump($ex);
        }
    }



    public function getById(int $id)
    {
        $query = "SELECT * FROM Answers WHERE answer_id = :answer_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['answer_id' => $id]);
        $answerData = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Answer(
            $answerData['user_id'],
            $answerData['question_id'],
            $answerData['body'],
            $answerData['created_at'],
            $answerData['answer_id'],
            $answerData['reputations']
        );
    }

    public function getAll()
    {
        $query = "SELECT * FROM Answers";
        $stmt = $this->db->query($query);
        $answerDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $answers = [];
        foreach ($answerDataArray as $answerData) {
            $answers[] = new Answer(
                $answerData['user_id'],
                $answerData['question_id'],
                $answerData['body'],
                $answerData['created_at'],
                $answerData['answer_id'],
                $answerData['reputations']
            );
        }
        return $answers;
    }

    public function update(int $id, object $data)
    {
        $query = "UPDATE Answers SET user_id = :user_id, question_id = :question_id, body = :body, created_at = :created_at, reputations = :reputations WHERE answer_id = :answer_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'answer_id' => $id,
            'user_id' => $data->user_id,
            'question_id' => $data->question_id,
            'body' => $data->body,
            'created_at' => $data->created_at,
            'reputations' => $data->reputations
        ]);
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM Answers WHERE answer_id = :answer_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['answer_id' => $id]);
    }

    public function getAnswersByQuestionID($questionID)
    {
        $query = "SELECT * FROM Answers WHERE question_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$questionID]); // Use array parameter binding for PDO
        $answersDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $answers = [];
        foreach ($answersDataArray as $answerInfo) {
            $answers[] = new Answer(
                $answerInfo['user_id'],
                $answerInfo['question_id'],
                $answerInfo['body'],
                $answerInfo['created_at'],
                $answerInfo['answer_id'],
                $answerInfo['reputations']
            );
        }
        return $answers;
    }

    public function getAnswersByUserID($userID)
    {
        try {

            $query = "SELECT * FROM Answers WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userID]); // Use array parameter binding for PDO
            $answersDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $answers = [];
            foreach ($answersDataArray as $answerInfo) {
                $answers[] = new Answer(
                    $answerInfo['user_id'],
                    $answerInfo['question_id'],
                    $answerInfo['body'],
                    $answerInfo['created_at'],
                    $answerInfo['answer_id'],
                    $answerInfo['reputations']
                );
            }
            return $answers;
        } catch (Exception $ex) {
            var_dump($ex);
        }

        return [];
    }
}

?>