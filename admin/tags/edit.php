<?php
require_once __DIR__ . "/../../vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/../partials/admin_header.php";
}

use App\Services\TagService;

$tagService = new TagService();

if (!isset($_GET["tag_id"])) {
    header("Location: tags.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tag_id = $_POST["tag_id"];
    $name = $_POST["name"];

    $tagService = new TagService();

    $tagService->update($tag_id, (object) ["name" => $name]);

    header("Location: .");
    exit();
}

$tag_id = $_GET["tag_id"];

$tag = $tagService->getById($tag_id);

if (!$tag) {
    header("Location: tags.php");
    exit();
}
?>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card mt-5">
                <div class="card-header">
                    <h2 class="display-6 text-center">Edit Tag</h2>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?php echo $tag->getName(); ?>" required>
                        </div>
                        <input type="hidden" name="tag_id" value="<?php echo $tag->getTagId(); ?>">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>