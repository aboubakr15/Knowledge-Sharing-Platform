<?php
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../Utils/config.php";
use App\Services\UserService;

$userService = new UserService();

$user = null;
$message = '';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    $user = $userService->getById($userId);

    if (!$user) {
        $message = 'User not found.';
    }
} else {
    $message = 'User ID is missing.';
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $user) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($username === $user->username && $email === $user->email && $role === $user->role) {
        $message = "No changes made.";
    } else {
        if (!empty($username) && !empty($email) && !empty($role)) {
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setRole($role);

            $success = $userService->update($userId, $user);

            if ($success) {
                $message = "User updated successfully";
            } else {
                $message = 'Failed to update user';
            }
        } else {
            $message = 'Please fill out all fields';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="<?php echo $domain; ?>/css/bootstrap.min.css" />
    <style>
        body {
            padding-top: 56px;
        }

        .container {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <nav class="navbar navbar-dark navbar-expand-lg bg-dark fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="EditUser.php?id=<?php echo $user->user_id; ?>">Edit User</a>
            </div>
        </nav>

        <?php if (!empty($message)): ?>
            <div class="alert alert-primary" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if ($user): ?>
            <form action="EditUser.php?id=<?php echo $user->user_id; ?>" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?php echo $user->username; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $user->email; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="admin" <?php echo ($user->role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?php echo ($user->role === 'user') ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-danger">Go Back</a>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>