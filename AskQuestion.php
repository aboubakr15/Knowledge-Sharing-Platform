<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/partials/header.php";
}

require_once __DIR__ . "/vendor/autoload.php";

use App\Services\QuestionService;
use App\Services\TagService;
use App\Services\UserService;
use App\Services\AuthService;

$questionService = new QuestionService();
$tagService = new TagService();
$userService = new UserService();
$authService = new AuthService();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $title = $_POST["title"];
    $body = $_POST["body"];
    $tags = explode(",", $_POST["tags"]); // Assuming tags are entered as comma-separated values

    // Create an object with question data
    $questionData = new stdClass();
    $questionData->user_id = $authService->getCurrentUser()->user_id;
    $questionData->title = $title;
    $questionData->body = $body;
    $questionData->created_at = date('Y-m-d H:i:s');
    $questionData->updated_at = date('Y-m-d H:i:s');
    $questionData->reputations = 0; // Set reputations to default value

    // Insert question into database
    $questionId = $questionService->create($questionData);

    // Redirect to question details page with question ID
    if ($questionId) {
        foreach ($tags as $tagName) {
            // Add tags to the question
            $tagData = new stdClass();
            $tagData->name = $tagName;
            $tagId = $tagService->create($tagData);
            $questionService->add_tag_to_question($questionId, $tagId);
        }
        header("Location: question_details.php?id=" . $questionId);
        exit();
    } else {
        echo "Error: Failed to insert question.";
    }
}

?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Ask a Question</div>
                <div class="card-body">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="body" class="form-label">Body</label>
                            <textarea class="form-control" id="body" name="body" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags (comma-separated)</label>
                            <input type="text" class="form-control" id="tags" name="tags" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>