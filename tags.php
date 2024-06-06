<?php
require_once __DIR__ . "/partials/header.php";
require_once __DIR__ . "/vendor/autoload.php";

use App\Services\TagService;

$tagService = new TagService();
$tags = $tagService->getAllTagsOrderedByQuestionCount();

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">All Tags</h2>
            <div class="list-group">
                <?php foreach ($tags as $tag): ?>
                    <a href="index.php?tag=<?php echo $tag->getTagId(); ?>"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <?php echo $tag->getName(); ?>
                        <span class="badge bg-primary"><?php echo $tag->getQuestionCount(); ?> questions</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/partials/footer.php"; ?>