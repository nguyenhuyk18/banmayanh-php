<?php foreach($product->getComments() as $comment): ?>
<hr>
<span class="date pull-right"><?= $comment->getCreatedDate() ?></span>
<input class="answered-rating-input" name="rating" type="text" title="" value="<?= $comment->getStar() ?>" readonly />
<span class="by"><?= h($comment->getFullName()) ?></span>
<p><?= h($comment->getDescription()) ?></p>
<?php endforeach;
