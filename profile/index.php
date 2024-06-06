<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Utils/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/../partials/header.php";
}

use App\Services\QuestionService;
use App\Services\UserService;

$userService = new UserService();
$questionService = new QuestionService();
$user = isset($_GET['id']) ? $userService->getByID($_GET['id']) : $authService->getCurrentUser();
$userId = $user->getUserId();
$currentUserId = $authService->getCurrentUser()->getUserId();
$questions = $questionService->getQuestionsByUserID($user->getUserId());
$badges = $userService->getbadgesByUserId($userId);
?>

<style>
    .badge-bronze {
        background-color: #CD7F32;
        color: white;
    }

    .badge-silver {
        background-color: #c0c0c0;
        color: black;
    }

    .badge-gold {
        background-color: #ffd700;
        color: black;
    }
</style>


<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3><?php echo $user->username; ?></h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="<?php echo $domain . $user->photo; ?>" class="img-fluid" alt="User Photo">
                </div>
                <div class="col-md-8">
                    <p>Email: <?php echo $user->email; ?></p>
                    <p>Reputations: <?php echo $user->reputations; ?></p>
                    <?php
                    if (empty($badges))
                        echo "<h3>No badges yet</h3>";
                    ?>
                    <ul class="list-group">
                        <?php
                        foreach ($badges as $userBadge) {
                            $badgeClass = '';
                            switch ($userBadge['type']) {
                                case 'Beginner':
                                    $badgeClass = 'badge-bronze';
                                    break;
                                case 'Intermediate':
                                    $badgeClass = 'badge-silver';
                                    break;
                                case 'Advanced':
                                    $badgeClass = 'badge-gold';
                                    break;
                                default:
                                    $badgeClass = '';
                                    break;
                            }
                            ?>
                            <li class="list-group-item <?php echo $badgeClass; ?>">
                                <?php echo $userBadge['type']; ?> -
                                <?php echo $userBadge['name']; ?>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <?php if ($user->getUserId() == $currentUserId): ?>
                <form action="updateprofile.php" method="GET">
                    <input type="hidden" name="user_id" value="<?php echo $user->getUserId(); ?>">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
                <a href="viewownanswers.php" class="btn btn-primary">View Own Answers</a>
                <a href="viewownbadges.php" class="btn btn-primary">View Own Badges</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h2>Questions</h2>
    <hr>
    <div class="card">
        <div class="card-header">
            Question List
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php
                foreach ($questions as $question) {
                    echo '<li class="list-group-item">';
                    echo '<h5>' . $question->title . '</h5>';
                    echo '<p>' . $question->body . '</p>';
                    echo '<small>Created at: ' . $question->created_at . '</small><br />';
                    echo "<a class='btn btn-outline-primary' href='{$domain}question_details.php?id={$question->getQuestionId()}'>Go</a>";
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>