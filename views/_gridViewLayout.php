<?php

/**
 * User this render as list/grid layout.
 * Like: $this->render('@app/views/_gridViewLayout')
 */

/**
 * Vars helper. Under the cut
 *
 * @var \yii\web\View $this
 */
?>
<div class='row'>
    {items}
</div>
<div class='row'>
    <div class='col-xs-12 text-center mt-5'>{pager}</div>
</div>