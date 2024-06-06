<?php

namespace App\Services;

use App\Models\User;
use App\Utils\DBConnection;
use PDO;
use Exception;

class UserService implements Service
{
    private $db;

    public function __construct()
    {
        $this->db = DBConnection::getConnection();
    }

    //changed
    public function create(object $data): int
    {
        $query = "INSERT INTO Users (username, email, password, photo, created_at, reputations, role) 
                  VALUES (:username, :email, :password, :photo, :created_at, :reputations, :role)";
        $stmt = $this->db->prepare($query);
        $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT); // Hash the password
        $stmt->execute([
            'username' => $data->username,
            'email' => $data->email,
            'password' => $hashedPassword,
            'photo' => $data->photo,
            'created_at' => $data->created_at,
            'reputations' => $data->reputations != null ? $data->reputations : 0,
            'role' => $data->role
        ]);
        return $this->db->lastInsertId();
    }

    public function getById(int $id): ?User
    {
        try {
            $query = "SELECT * FROM Users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $id]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                return new User(
                    $userData['user_id'],
                    $userData['username'],
                    $userData['email'],
                    $userData['password'],
                    $userData['photo'],
                    $userData['created_at'],
                    $userData['reputations'],
                    $userData['role']
                );
            }

            return null;
        } catch (\PDOException $e) {
            // Handle PDOException
            // Log the error or throw a custom exception
            throw new \Exception("Error fetching user by ID: " . $e->getMessage());
        }
    }

    public function getByUsername(string $username): ?User
    {
        try {
            $query = "SELECT * FROM Users WHERE LOWER(username) = LOWER(:username)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['username' => $username]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                return new User(
                    $userData['user_id'],
                    $userData['username'],
                    $userData['email'],
                    $userData['password'],
                    $userData['photo'],
                    $userData['created_at'],
                    $userData['reputations'],
                    $userData['role']
                );
            }

            return null;
        } catch (\PDOException $e) {
            // Handle PDOException
            // Log the error or throw a custom exception
            throw new \Exception("Error fetching user by username: " . $e->getMessage());
        }
    }


    public function getAll(): array
    {
        $query = "SELECT * FROM Users";
        $stmt = $this->db->query($query);
        $userDataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($userDataArray as $userData) {
            $users[] = new User(
                $userData['user_id'],
                $userData['username'],
                $userData['email'],
                $userData['password'],
                $userData['photo'],
                $userData['created_at'],
                $userData['reputations'],
                $userData['role']
            );
        }
        return $users;
    }

    //changed
    public function update(int $id, object $data): bool
    {
        try {
            $query = "UPDATE Users SET username = :username, email = :email, 
                      photo = :photo, role = :role, reputations = :reputations 
                      WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'user_id' => $id,
                'username' => isset($data->username) ? $data->username : null,
                'email' => isset($data->email) ? $data->email : null,
                'photo' => isset($data->photo) ? $data->photo : null,
                'role' => isset($data->role) ? $data->role : null,
                'reputations' => isset($data->reputations) ? $data->reputations : 0
            ]);
            return true;
        } catch (\PDOException $e) {
            echo "<pre>";
            var_dump($e);
            echo "</pre>";
            // If an exception occurs during the execution of the query,
            // return false indicating that the update was not successful
            return false;
        }
    }

    public function delete(int $id): void
    {
        try {
            // Delete related records in the answers table
            $query = "DELETE FROM Answers WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $id]);

            // Delete the user
            $query = "DELETE FROM Users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $id]);
        } catch (\PDOException $e) {
            // Handle the exception
            // Log the error or throw a custom exception
            throw new \Exception("Error deleting user: " . $e->getMessage());
        }
    }

    public function getUserByUsername(string $username)
    {
        $query = "SELECT * FROM Users WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['username' => $username]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            return new User(
                $userData['user_id'],
                $userData['username'],
                $userData['email'],
                $userData['password'],
                $userData['photo'],
                $userData['created_at'],
                $userData['reputations'],
                $userData['role']
            );
        }
        return null;
    }

    public function getUserByEmail(string $email)
    {
        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => $email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            return new User(
                $userData['user_id'],
                $userData['username'],
                $userData['email'],
                $userData['password'],
                $userData['photo'],
                $userData['created_at'],
                $userData['reputations'],
                $userData['role']
            );
        }
        return null;
    }

    public function getUsersJoinedStatistics(): array
    {
        // Get count of users joined in the last day, week, and month
        $query = "SELECT COUNT(*) AS count FROM Users WHERE created_at >= CURDATE() - INTERVAL 1 DAY";
        $usersJoinedLastDay = $this->db->query($query)->fetch(PDO::FETCH_ASSOC)['count'];

        $query = "SELECT COUNT(*) AS count FROM Users WHERE created_at >= CURDATE() - INTERVAL 1 WEEK";
        $usersJoinedLastWeek = $this->db->query($query)->fetch(PDO::FETCH_ASSOC)['count'];

        $query = "SELECT COUNT(*) AS count FROM Users WHERE created_at >= CURDATE() - INTERVAL 1 MONTH";
        $usersJoinedLastMonth = $this->db->query($query)->fetch(PDO::FETCH_ASSOC)['count'];

        return [
            'last_day' => $usersJoinedLastDay,
            'last_week' => $usersJoinedLastWeek,
            'last_month' => $usersJoinedLastMonth
        ];
    }

    public function getUsersWithMostBadges(): array
    {
        try {
            $query = "SELECT u.username, COUNT(ub.badge_id) as badge_count 
                      FROM Users u
                      INNER JOIN User_Badges ub ON u.user_id = ub.user_id
                      GROUP BY u.user_id 
                      ORDER BY badge_count DESC 
                      LIMIT 5";
            $statement = $this->db->query($query);
            $users = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $users;
        } catch (\PDOException $e) {
            // Handle PDOException
            throw new Exception("Error fetching users with most badges: " . $e->getMessage());
        }
    }


    public function getUsersWithHighestReputations(int $limit = 10): array
    {
        // Get list of users with highest reputations
        $query = "SELECT * FROM Users ORDER BY reputations DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBadgesByUserId(int $userId): array
    {
        try {
            // Prepare the SQL query
            $query = "SELECT b.name, b.type 
                      FROM User_Badges ub
                      INNER JOIN Badges b ON ub.badge_id = b.badge_id
                      WHERE ub.user_id = :user_id";

            // Prepare and execute the statement
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userId]);

            // Fetch the badges
            $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $badges;
        } catch (\PDOException $e) {
            // Handle PDOException
            // Log the error or throw a custom exception
            throw new Exception("Error fetching badges by user ID: " . $e->getMessage());
        }
    }

    public function increaseReputations(int $userId, int $rep)
    {
        try {
            $query = "UPDATE Users SET reputations = reputations + :rep WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);

            $stmt->execute(['rep' => $rep, 'user_id' => $userId]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("User with ID $userId not found.");
            }
        } catch (Exception $ex) {
            var_dump($ex);
        }
    }

}