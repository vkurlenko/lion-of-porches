<?php
$posts = query_posts('category__in=154');

if($posts):?>

    <?php foreach($posts as $post):?>

        <div class="col-xs-12 col-md-4"><h4><?=$post->post_title?></h4>
            <p><?=$post->post_content?></p>
        </div>

    <?php endforeach; ?>

<?php endif; ?>