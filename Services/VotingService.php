<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Vote;
use App\Utils\DBConnection;
use PDO;

class VotingService implements Service
{
    private $db;
    private $notificationService;

    public function __construct()
    {
        $this->db = DBConnection::getConnection();
        $this->notificationService = new NotificationService();
    }

    public function create(object $data)
    {

    }

    public function add_vote(object $data, string $for = "question")
    {
        try {
            $targeted_user_id = $for == "question" ? (new QuestionService())->getById($data->getQuestionId())->getUserId() : (new AnswerService())->getById($data->getAnswerId())->getUserId();

            if ($for == "question") {
                // Check if the user has already voted on this question
                $query = "SELECT * FROM Votes WHERE question_id = :question_id AND user_id = :user_id";
                $stmt = $this->db->prepare($query);
                $stmt->execute(['question_id' => $data->getQuestionId(), 'user_id' => $data->getUserId()]);
                $voteData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($voteData) {
                    // Update the existing vote
                    $query = "UPDATE Votes SET type = :type WHERE vote_id = :vote_id";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute(['type' => $data->getVoteType(), 'vote_id' => $voteData['vote_id']]);
                    return false;
                } else {
                    // Insert a new vote
                    $query = "INSERT INTO Votes (user_id, question_id, type) VALUES (:user_id, :question_id, :type)";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([
                        'user_id' => $data->getUserId(),
                        'question_id' => $data->getQuestionId(),
                        'type' => $data->getVoteType()
                    ]);

                }
                $notificationData = new Notification(
                    0,
                    $data->getUserId(),
                    $targeted_user_id,
                    'vote_on_question',
                    $data->getQuestionId(),
                    false
                );

                $this->notificationService->create($notificationData);

                return true;
            } else {
                $query = "SELECT * FROM Votes WHERE answer_id = :answer_id AND user_id = :user_id";
                $stmt = $this->db->prepare($query);
                $stmt->execute(['answer_id' => $data->getAnswerId(), 'user_id' => $data->getUserId()]);
                $voteData = $stmt->fetch(PDO::FETCH_ASSOC);


                if ($voteData) {
                    // Update the existing vote
                    $query = "UPDATE Votes SET type = :type WHERE vote_id = :vote_id";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute(['type' => $data->getVoteType(), 'vote_id' => $voteData['vote_id']]);
                    return false;
                } else {
                    // Insert a new vote
                    $query = "INSERT INTO Votes (user_id, answer_id, type) VALUES (:user_id, :answer_id, :type)";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([
                        'user_id' => $data->getUserId(),
                        'answer_id' => $data->getAnswerId(),
                        'type' => $data->getVoteType()
                    ]);
                }

                $notificationData = new Notification(
                    0,
                    $data->getUserId(),
                    $targeted_user_id,
                    'vote_on_answer',
                    $data->getAnswerId(),
                    false,
                );

                $this->notificationService->create($notificationData);

                return true;
            }
        } catch (\Exception $ex) {
            var_dump($ex);
        }
    }


    public function getById(int $id)
    {
        $query = "SELECT * FROM Votes WHERE vote_id = :vote_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['vote_id' => $id]);
        $voteData = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Vote($voteData['vote_id'], $voteData['user_id'], $voteData['question_id'], $voteData['type']);
    }

    public function getAll()
    {
        $query = "SELECT * FROM Votes";
        $stmt = $this->db->query($query);
        $voteDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $votes = [];
        foreach ($voteDataArray as $voteData) {
            $votes[] = new Vote($voteData['vote_id'], $voteData['user_id'], $voteData['question_id'], $voteData['type']);
        }
        return $votes;
    }

    public function update(int $id, object $data)
    {
        $query = "UPDATE Votes SET user_id = :user_id, question_id = :question_id, type = :type WHERE vote_id = :vote_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'vote_id' => $id,
            'user_id' => $data->user_id,
            'question_id' => $data->question_id,
            'type' => $data->type
        ]);
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM Votes WHERE vote_id = :vote_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['vote_id' => $id]);
    }

    public function getUpvotesCount(int $questionId)
    {
        $query = "SELECT COUNT(*) AS upvotes_count FROM Votes WHERE question_id = :question_id AND type = 'upvote'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['question_id' => $questionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['upvotes_count'];
    }

    public function getDownvotesCount(int $questionId)
    {
        $query = "SELECT COUNT(*) AS downvotes_count FROM Votes WHERE question_id = :question_id AND type = 'downvote'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['question_id' => $questionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['downvotes_count'];
    }

    public function deleteByUserIDAndQuestionID(int $user_id, int $question_id)
    {
        $query = "DELETE FROM Votes WHERE user_id = :user_id AND question_id = :question_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['user_id' => $user_id, 'question_id' => $question_id]);
    }

    public function getUpvotesCountForAnswers(int $answerId): int
    {
        $query = "SELECT COUNT(*) AS upvotes_count FROM Votes WHERE answer_id = :answer_id AND type = 'upvote'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['answer_id' => $answerId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['upvotes_count'];
    }

    public function getDownvotesCountForAnswers(int $answerId): int
    {
        $query = "SELECT COUNT(*) AS downvotes_count FROM Votes WHERE answer_id = :answer_id AND type = 'downvote'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['answer_id' => $answerId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['downvotes_count'];
    }
}
