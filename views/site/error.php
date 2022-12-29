<?php

use yii\helpers\Html;

/**
 * Vars helper. Under the cut
 *
 * @var \yii\web\View $this
 * @var string $name
 * @var string $message
 */

$this->title = $name;
?>
<h1 class="mt-5"><?= Html::encode($this->title) ?></h1>

<?php if ($message) { ?>
    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>
<?php } ?>

<p>
    The above error occurred while the Web server was processing your request.
</p>
<p>
    Please contact us if you think this is a server error. Thank you.
</p>