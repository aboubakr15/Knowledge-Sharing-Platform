<?php

namespace App;

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/partials/header.php";

use App\Services\AnswerService;

$answerService = new AnswerService();

if (isset($_POST['answer_id']) && !empty($_POST['answer_id'])) {
    $answerId = $_POST['answer_id'];
    $answer = $answerService->getById($answerId);
    $questionId = $answer->getQuestionId();
} else {
    // Redirect or display an error message if no answer ID is provided
    header("Location: index.php");
    exit();
}

if (isset($_POST['update_answer'])) {
    // Get form data
    $newAnswerText = $_POST['new_answer_text'];
    $answer->setBody($newAnswerText);
    $answerService->update($answerId, $answer);

    // Redirect back to view specific question page
    header("Location: question_details.php?id=$questionId");
    exit();
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Edit Your Answer</h5>
                    <form method="post">
                        <input type="hidden" name="answer_id" value="<?php echo $answerId ?>">
                        <div class="form-group">
                            <label for="body">Body:</label>
                            <textarea class="form-control" id="body" name="new_answer_text" rows="3"
                                placeholder="Enter your edited answer here"
                                required><?php echo $answer->getBody(); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_answer">Update Answer</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . "/partials/footer.php"; ?>