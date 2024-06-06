<?php

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../Utils/config.php";

use App\Services\UserService;

// Initialize the UserService
$userService = new UserService();

// Define variables to hold the alert message and alert type
$alertMessage = '';
$alertType = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate form data (You might want to add more validation)
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $alertMessage = 'Please fill out all fields';
        $alertType = 'danger';
    } else {
        try {

            // Create an array with user data
            $userData = (object) [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'photo' => null,
                'created_at' => date('Y-m-d H:i:s'), // Current datetime
                'reputations' => 0, // Set initial reputation to 0
                'role' => $role // Set the role based on the selected value
            ];

            // Call the create method of UserService to add the user to the database
            $userId = $userService->create($userData);

            if ($userId) {
                // User added successfully
                $alertMessage = "User added successfully";
                $alertType = 'success';
            } else {
                // Failed to add user
                $alertMessage = 'Failed to add user';
                $alertType = 'danger';
            }
        } catch (PDOException $e) {
            // Handle PDO exceptions (SQL errors)
            $alertMessage = 'Error: ' . $e->getMessage();
            $alertType = 'danger';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="<?php echo $domain; ?>/css/bootstrap.min.css" />
    <style>
        body {
            padding-top: 56px;
            /* Adjust based on your navbar height */
        }

        .container {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-expand-lg bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="AddUser.php">Add User</a>
            <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">View Users</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Alert message -->
        <?php if (!empty($alertMessage)): ?>
            <div class="alert alert-<?php echo $alertType; ?>" role="alert">
                <?php echo $alertMessage; ?>
            </div>
        <?php endif; ?>

        <!-- User form -->
        <form action="AddUser.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

</body>

</html>