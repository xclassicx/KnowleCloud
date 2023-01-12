<?php

namespace app\widgets;

use yii\bootstrap5\LinkPager;
use yii\widgets\ListView;

/**
 * Небольшое расширение, позволяет указывать имя переменой с моделью для view, а не только 'model'
 */
class GridView extends ListView
{
    /**
     * bootstrap5 паждинатор
     *
     * @var array
     */
    public $pager = [
        'class'       => LinkPager::class,
        'listOptions' => ['class' => ['pagination justify-content-center']],
    ];

    public string $sModelViewName = 'model';

    /**
     * Renders a single data model.
     *
     * @param mixed $model the data model to be rendered
     * @param mixed $key   the key value associated with the data model
     * @param int $index   the zero-based index of the data model in the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderItem($model, $key, $index): string
    {
        if ($this->itemView === null) {
            $content = $key;
        } elseif (is_string($this->itemView)) {
            $content = $this->getView()->render($this->itemView, array_merge([
                $this->sModelViewName => $model,
                'key'                 => $key,
                'index'               => $index,
                'widget'              => $this,
            ], $this->viewParams));
        } else {
            $content = call_user_func($this->itemView, $model, $key, $index, $this);
        }

        return $content;
    }
}
