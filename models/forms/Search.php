<?php

namespace app\models\forms;

use yii\base\Model;

class Search extends Model
{
    // Form fields
    const QUERY_STRING_MIN = 3;
    const QUERY_STRING_MAX = 100;

    public string $q = '';

    public function rules(): array
    {
        return [
            [['q'], 'trim'],
            [['q'], 'required'],
            [['q'], 'string', 'min' => self::QUERY_STRING_MIN, 'max' => self::QUERY_STRING_MAX],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'q' => 'Что ищем?',
        ];
    }

    public function attributeHints(): array
    {
        return [
            'q' => 'от ' . self::QUERY_STRING_MIN . ' и не более ' . self::QUERY_STRING_MAX . ' символов',
        ];
    }
}