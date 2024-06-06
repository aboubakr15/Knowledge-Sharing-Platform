<?php
require_once __DIR__ . "/vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/partials/header.php";
}

use App\Services\UserService;
use App\Utils\Utils;
use App\Utils\Validator;

$userService = new UserService();

$error = ""; // Initialize error variable

if (isset($_POST['login'])) {
    $username = Utils::clean_input($_POST["username"]);
    $password = Utils::clean_input($_POST["password"]);

    // Validate inputs
    if (!Validator::username($username)) {
        $error = "Invalid username. Username should be between 4 and 20 characters.";
    } elseif (!Validator::password($password)) {
        $error = "Invalid password. Password should be at least 8 characters long.";
    } else {
        $user = $userService->getUserByUsername($username);

        if ($user && $password == $user->password) {
            (new \App\Services\AuthService())->auth($username);
            header("Location: " . $domain . 'index.php');
            exit();
        } else {
            $error = "Invalid username or password. Please try again.";
        }
    }

    if ($error) {
        header("Location: " . $domain . "login.php?error=" . $error);
    }
}

$error = $_GET['error'];
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Login</div>

                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" name="login" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>