<?php

require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/partials/header.php";

use App\Models\Question;
use App\Services\QuestionService;
use App\Services\TagService;

$questionService = new QuestionService();

$tagService = new TagService();
$tags = $tagService->getAll();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $questionId = $_GET['id'];
    $question = $questionService->getById($questionId);
} else {
    // Redirect or display an error message if no question ID is provided
    header("Location: index.php");
    exit();
}

if (isset($_POST['edit_question'])) {
    // Get form data
    $questionID = $_POST['question_id'];
    $title = $_POST['title'];
    $body = $_POST['body'];

    $updated_question = new Question($questionID, $question->getUserId(), $title,
                                    $body, $question->getCreatedAt(), $question->getUpdatedAt(),
                                    $question->getReputations());

    $questionService->update($questionID, $updated_question);

    // Redirect back to view specific question page
    header("Location: question_details.php?id=$questionID");
    exit();
}

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="post">
                <input type="hidden" name="question_id" value="<?php echo $question->getQuestionID(); ?>">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" class="form-control" id="title" name="title"
                        value="<?php echo $question->getTitle(); ?>">
                </div>
                <div class="form-group">
                    <label for="body">Body:</label>
                    <textarea class="form-control" id="body" name="body"><?php echo $question->getBody(); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary" name="edit_question">Submit</button>
            </form>
        </div>
    </div>
</div>