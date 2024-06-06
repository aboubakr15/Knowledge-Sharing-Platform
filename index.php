<?php

require_once __DIR__ . "/vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/partials/header.php";
}

use App\Services\QuestionService;
use App\Services\TagService;
use App\Services\UserService;
use App\Utils\Utils;

$questionService = new QuestionService();
$questions = $questionService->getAll();

$tagService = new TagService();
$tags = $tagService->getAll();

$userService = new UserService();

$selected_tag_id = isset($_GET['tag']) ? $_GET['tag'] : null;
if ($selected_tag_id) {
    $questions = $questionService->getQuestionsByTagID($selected_tag_id);
} else {
    $questions = $questionService->getAll();
}

?>
<style>
    .form-group.mb-2 select.form-control {
        width: 100%;
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        border-radius: 0.25rem;
        background-color: #f8f9fa;
        border-color: #ced4da;
        color: #495057;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .btn-filter {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
        border-radius: 0.25rem;
        transition: all 0.3s ease-in-out;
    }

    .btn-filter:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if ($authService->isLoggedIn()): ?>
                <div class="text-center mt-3">
                    <a href="<?php echo $domain; ?>/AskQuestion.php" class="btn btn-primary mb-5"> <i
                            class="fas fa-plus"></i> Ask Question</a>
                </div>
            <?php else: ?>
                <div class="text-center mt-3">
                    <button onclick="alert('Login to answer questions')" class="btn btn-primary mb-5">Ask Question</button>
                </div>
            <?php endif; ?>


            <form class="input-group mb-3">
                <select class="form-control" id="tag" name="tag">
                    <option value="">All Tags</option>
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?php echo $tag->getTagID(); ?>" <?php echo ($selected_tag_id == $tag->getTagID()) ? 'selected' : ''; ?>><?php echo $tag->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
            <!-- Display questions -->
            <?php foreach ($questions as $question): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $userService->getById($question->getUserID())->photo; ?>" alt="User Photo"
                                class="rounded-circle me-2" style="width: 40px; height: 40px;">
                            <div>
                                <h6 class="mb-0"><?php echo $userService->getById($question->getUserID())->username; ?></h6>
                                <small class="text-muted"><?php echo Utils::time_since($question->getCreatedAt()) ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $question->getTitle(); ?></h5>
                        <p class="card-text">
                            <?php foreach ($tagService->getTagsByQuestionID($question->getQuestionID()) as $tag): ?>
                                <span class="badge text-bg-primary"><?php echo $tag->getName(); ?></span>
                            <?php endforeach; ?>
                        </p>
                        <p class="card-text"><?php echo $question->getBody(); ?></p>
                        <a href="question_details.php?id=<?php echo $question->getQuestionID(); ?>"
                            class="btn btn-outline-primary btn-sm btn-view-question">Read more</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>