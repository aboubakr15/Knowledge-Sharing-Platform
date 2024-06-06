<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/../partials/header.php";
}

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Utils/config.php";

use App\Services\UserService;
use App\Services\AuthService;

$userService = new UserService();
$authService = new AuthService();
$error = '';

$user = $authService->getCurrentUser();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';

    $password = $_POST['password'];

    $targetDir = __DIR__ . '/../uploads/';

    try {
        if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $targetFile = $targetDir . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
            $photo = 'uploads/' . basename($_FILES['photo']['name']);
        } else {
            $photo = $user->photo;
        }
    } catch (Exception $ex) {
        var_dump($ex);
    }

    $user->username = $username;
    $user->email = $email;
    if ($password !== null) {
        $user->password = $password;
    }
    $user->photo = $photo;

    try {
        $userService->update($user->user_id, $user);
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $error = "An error occurred while updating the profile: " . $e->getMessage();
    }
}
?>

<div class="container mt-5">
    <h2>Update Profile</h2>
    <hr>
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username"
                value="<?php echo htmlspecialchars($user->username); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?php echo htmlspecialchars($user->email); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="photo">Photo</label>
            <input type="file" class="form-control-file" id="photo" name="photo">
            <?php if ($user->photo): ?>
                <img src="<?php echo htmlspecialchars($user->photo); ?>" alt="Current Photo"
                    style="max-width: 200px; margin-top: 10px;">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>