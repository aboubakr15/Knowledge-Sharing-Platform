<?php
use App\Services\AuthService;
use App\Services\NotificationService;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Utils/config.php';

$authService = new AuthService();
$notificationService = new NotificationService();
if ($authService->isLoggedIn()) {
    $unReadNotifications = $notificationService->getLimit($authService->getCurrentUser()->getUserId(), 30);
    $unReadNotificationsCount = count($unReadNotifications);
}

?>
<html>

<head>
    <title>Title</title>
    <link rel="stylesheet" href="<?php echo $domain; ?>/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo $domain; ?>/css/all.min.css" />
</head>

<body>
    <nav class="navbar navbar-dark navbar-expand-lg bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $domain; ?>">Knowledge</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?php echo $domain; ?>">Home</a>
                    </li>
                    <!-- Add Browse Tags link here -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $domain; ?>tags.php">Browse Tags</a>
                    </li>
                    <!-- End of Browse Tags link -->
                    <?php if (!$authService->isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $domain; ?>login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $domain; ?>register.php">Register</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $domain . '/profile/index.php'; ?>">
                                <img src="<?php echo $domain . $authService->getCurrentUser()->photo; ?>" alt="User Photo"
                                    class="user-photo rounded-circle mx-2" style="width: 32px; height: 32px;">
                                Profile
                            </a>
                        </li>
                        <?php if ($authService->getCurrentUser()->getRole() == "admin"): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $domain; ?>/admin/index.php">Admin panel</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link logout-btn" href="<?php echo $domain; ?>logout.php">Logout</a>
                        </li>
                    <?php endif ?>
                </ul>
                <div class="dropdown m-2">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="notificationsDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-regular fa-bell"></i>
                        <?php echo $unReadNotificationsCount; ?>
                    </button>
                    <?php if ($authService->isLoggedIn()): ?>
                        <ul class="dropdown-menu" aria-labelledby="notificationsDropdown">
                            <?php
                            if (empty($unReadNotifications)) {
                                echo "<li>0 notifcations</li>";
                            } else {
                                foreach ($unReadNotifications as $notification) {
                                    echo "<li><a class='dropdown-item' href='" . $domain . "questions_details.php?id=" . $notification->getSourceId() . "'>" . $notification->displayMessage() . "</a></li>";
                                }
                            }
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <form class="d-flex" role="search" action="<?php echo $domain; ?>/Search.php" method="GET"
                    id="search-form">
                    <input id="search-input" name="q" class="form-control me-2" type="search" placeholder="Search"
                        aria-label="Search">
                    <button class="btn btn-outline-success" id="search-btn" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>


    <div class="modal fade" id="logoutConfirmationModal" tabindex="-1" aria-labelledby="logoutConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutConfirmationModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="logoutLink" href="<?php echo $domain; ?>logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo $domain; ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo $domain; ?>/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var logoutButton = document.querySelector('.logout-btn');
            logoutButton.addEventListener('click', function (event) {
                event.preventDefault();
                var logoutConfirmationModal = new bootstrap.Modal(document.getElementById(
                    'logoutConfirmationModal'));
                logoutConfirmationModal.show();
            });

            var notificationsDropdown = document.getElementById('notificationsDropdown');
            notificationsDropdown.addEventListener('click', function (event) {
                event.preventDefault();
                // Make an AJAX call to mark all notifications as read
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo $domain; ?>mark_all_notifications_read.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        console.log("Success");
                    } else {
                        console.error('Failed to mark notifications as read');
                    }
                };
                xhr.send();
            });
        });
    </script>