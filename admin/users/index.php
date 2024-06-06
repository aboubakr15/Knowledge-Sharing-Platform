<?php
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../Utils/config.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/../partials/admin_header.php";
}

use App\Services\UserService;

$userService = new UserService();

$users = [];
$error = '';

$users = $userService->getAll();
?>
<div class="container p-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <a href="<?php echo $domain; ?>admin/users/AddUser.php" class="btn btn-primary mb-3">
        Add user
    </a>


    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($users) {
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . $user->user_id . "</td>";
                    echo "<td>" . $user->username . "</td>";
                    echo "<td>" . $user->email . "</td>";
                    echo "<td>" . $user->role . "</td>";
                    echo "<td>";
                    echo "<a href='EditUser.php?id=" . $user->user_id . "' class='btn btn-primary btn-sm'>Edit</a>";
                    echo "&nbsp;";
                    echo "<a href='DeleteUser.php?id=" . $user->user_id . "' class='btn btn-danger btn-sm'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>