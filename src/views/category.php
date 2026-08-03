<?php if (!empty($flash['message'])): ?>
    <div class="flash"><p><?= escape($flash['message']) ?></p></div>
<?php endif; ?>

<?php require_once view('includes/sidebar'); ?>

<div class="posts">
    <?php if(!$posts): ?>
        <h3 class="message">No posts in this category</h4>
    <?php else: ?>
        <?php foreach($posts as $post): ?>
            <div class="post">
                <span class="vote">
                    <?php if($user): ?>
                        <?php if(!$this->postModel->hasVoted($user->user_id, $post->post_id)): ?>
                            <a href="<?= base_url('/vote') ?>/<?= $post->post_id ?>"><img src="<?= asset('img/icons/vote.svg') ?>" alt="Vote"></a>
                        <?php else: ?>
                            <a href="<?= base_url('/unvote') ?>/<?= $post->post_id ?>"><img src="<?= asset('img/icons/unvote.svg') ?>" alt="Unvote"></a>
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?= asset('img/icons/vote.svg') ?>" alt="Vote">
                    <?php endif; ?>
          
                    <h4><?= $post->pts ?></h4>
                </span>

                <h3>
                    <?php if(isset($post->post_url)): ?>
                        <a href="/<?= $post->post_url ?>"><?= escape($post->post_title) ?></a>
                    <?php else: ?>
                        <a href="<?= base_url('/post') ?>/<?= escape($post->post_id) ?>"><?= escape($post->post_title) ?></a>
                    <?php endif; ?>
                </h3>

                <h5>
                    By
                    <?php if($post->user_username): ?>
                        <a href="<?= base_url('/profile') ?>/<?= escape($post->user_username) ?>"><?= escape($post->user_username) ?></a>
                    <?php else: ?>
                        [Deleted]
                    <?php endif; ?>
                    on
                    <?= format_date($post->post_date) ?>
                </h5>
        
                <h6><a href="<?= base_url('/post') ?>/<?= escape($post->post_id) ?>#comments">Comments</a></h6>
        
                <?php if($user && $post->post_by === $user->user_id): ?>
                    <h6><a href="<?= base_url('/edit') ?>/<?= $post->post_id ?>">Edit</a></h6>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="pagination">
        <?php if($p > 1): ?>
            <a id="prev" href="?p=<?php echo $p - 1 ?>">&#129168; Previous</a>
        <?php endif; ?>

        <?php if($p < $pages): ?>
            <a id="next" href="?p=<?php echo $p + 1 ?>">Next &#129170;</a>
        <?php endif; ?>
    </div>
</div>