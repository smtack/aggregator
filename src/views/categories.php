<?php require_once view('includes/sidebar'); ?>

<div class="posts">
    <div class="heading">
        <h2 id="heading">All Categories</h2>
    </div>

    <?php foreach($categories_list as $category): ?>
        <div class="post">
            <h3><a href="<?= base_url('/category') ?>/<?= escape($category->category_id) ?>"><?= escape($category->category_name) ?></a></h3>
            <p><?= escape($category->category_description) ?></p>
            <h5>
                Created by
                <?php if($category->user_username): ?>
                    <a href="<?= base_url('/profile') ?>/<?= escape($category->user_username) ?>"><?= escape($category->user_username) ?></a>
                <?php else: ?>
                    [Deleted]
                <?php endif; ?>
                on
                <?= format_date($category->category_created) ?>
            </h6>
        </div>
    <?php endforeach; ?>

    <div class="pagination">
        <?php if($p > 1): ?>
            <a id="prev" href="?p=<?php echo $p - 1 ?>">&#129168; Previous</a>
        <?php endif; ?>

        <?php if($p < $pages): ?>
            <a id="next" href="?p=<?php echo $p + 1 ?>">Next &#129170;</a>
        <?php endif; ?>
    </div>
</div>