<?php
// Include the necessary files
require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/../../Utils/config.php";
use App\Services\UserService;

// Create an instance of the UserService
$userService = new UserService();

// Initialize variables
$message = '';

// Check if user ID is provided in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Check if the user exists
    $user = $userService->getById($userId);
    $username = $user->username;

    if ($user) {
        // Check if confirmation is received
        if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
            // Delete the user
            $userService->delete($userId);
            $message = "User deleted successfully";
        } elseif (isset($_GET['confirm']) && $_GET['confirm'] === 'false') {
            // Confirmation rejected
            $message = "Deletion canceled";
        } else {
            // Ask for confirmation
            $message = "Are you sure you want to delete this user? </br> \"$username\" </br>
            <a href='DeleteUser.php?id=$userId&confirm=true'>Yes</a> 
            / 
            <a href='DeleteUser.php?confirm=false'>No</a>";
        }
    } else {
        $message = 'User not found.';
    }
} else {
    // If user ID is not provided, display an error message
    $message = 'Deletion canceled.';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
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
    <div class="container">
        <nav class="navbar navbar-dark navbar-expand-lg bg-dark fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="DeleteUser.php?id=<?php echo $user->user_id; ?>">Delete User</a>
            </div>
        </nav>

        <?php if (!empty($message)): ?>
        <div class="alert alert-primary" role="alert">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        <a href="index.php" class="btn btn-danger">Back to Users</a>
    </div>
</body>

</html>