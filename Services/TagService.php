<?php

namespace App\Services;

use App\Models\Tag;
use App\Utils\DBConnection;
use PDO;

class TagService implements Service
{
    private $db;

    public function __construct()
    {
        $this->db = DBConnection::getConnection();
    }

    public function create(object $data)
    {
        $tagName = $data->name;
        // Check if the tag already exists
        $existingTag = $this->getTagByName($tagName);

        if ($existingTag) {
            // Tag already exists, return its ID
            return $existingTag->tag_id;
        } else {
            // Tag does not exist, insert it into the database
            $query = "INSERT INTO Tags (name) VALUES (:name)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['name' => $tagName]);
            return $this->db->lastInsertId();
        }
    }

    public function getById(int $id)
    {
        $query = "SELECT * FROM Tags WHERE tag_id = :tag_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['tag_id' => $id]);
        $tagData = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Tag($tagData['tag_id'], $tagData['name']);
    }

    public function getAll()
    {
        $query = "SELECT * FROM Tags";
        $stmt = $this->db->query($query);
        $tagDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tags = [];
        foreach ($tagDataArray as $tagData) {
            $tags[] = new Tag($tagData['tag_id'], $tagData['name']);
        }
        return $tags;
    }

    public function update(int $id, object $data)
    {
        $query = "UPDATE Tags SET name = :name WHERE tag_id = :tag_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['tag_id' => $id, 'name' => $data->name]);
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM Tags WHERE tag_id = :tag_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['tag_id' => $id]);
    }

    public function getTagsByQuestionID($questionID)
    {
        $query = "SELECT Tags.tag_id, Tags.name FROM Tags 
                  INNER JOIN Question_Tags ON Tags.tag_id = Question_Tags.tag_id 
                  WHERE Question_Tags.question_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$questionID]); // Use array parameter binding for PDO
        $tagDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tags = [];
        foreach ($tagDataArray as $tagInfo) {
            $tags[] = new Tag($tagInfo['tag_id'], $tagInfo['name']);
        }
        return $tags;
    }

    public function getTagSuggestions(string $prefix): array
    {
        $prefix = strtolower($prefix) . '%'; // Convert to lowercase and add wildcard
        $query = "SELECT name FROM Tags WHERE LOWER(name) LIKE :prefix";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':prefix' => $prefix]);
        $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $suggestions;
    }


    private function getTagByName(string $tagName)
    {
        $query = "SELECT * FROM Tags WHERE name = :name";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['name' => $tagName]);
        $tagData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($tagData) {
            return new Tag($tagData['tag_id'], $tagData['name']);
        } else {
            return null;
        }
    }

    public function getAllTagsOrderedByQuestionCount()
    {
        $query = "SELECT Tags.*, COUNT(Question_Tags.tag_id) AS question_count
                FROM Tags
                LEFT JOIN Question_Tags ON Tags.tag_id = Question_Tags.tag_id
                GROUP BY Tags.tag_id
                ORDER BY question_count DESC";
        $stmt = $this->db->query($query);
        $tagsDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tags = [];
        foreach ($tagsDataArray as $tagData) {
            $tag = new Tag($tagData['tag_id'], $tagData['name']);
            $tag->setQuestionCount($tagData['question_count']);
            if ($tagData['question_count'] > 0)
                $tags[] = $tag;
        }

        return $tags;
    }

    public function getTagQuestionCount(int $id)
    {
        $query = "SELECT COUNT(question_id) AS question_count FROM Question_Tags WHERE tag_id = :tag_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['tag_id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['question_count'];
    }

}