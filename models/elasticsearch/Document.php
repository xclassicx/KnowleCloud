<?php

namespace app\models\elasticsearch;

use app\models\Document as DocumentModel;
use Yii;
use yii\elasticsearch\ActiveRecord;

class Document extends ActiveRecord
{
    const STORE_FIELDS = [
        'id',
        'name',
        'keywords',
        'file_extension',
    ];

    /**
     * Возвращает модель документа elasticsearch, соответствующий переданному документу.
     * Метод пытается найти документ в elasticsearch, если не находит - создает новый.
     * В любом случае поля документа заполняются данными переданной модели.
     *
     * Нужно для избежания коллизий, когда моделька создается через new,
     * наполняется данными и при попытке сохранения выясняется, что такая уже есть в elasticsearch.
     *
     * \app\models\elasticsearch\Document::factory($mDocumentModel)->save(); - безопасна от таких коллизий
     *
     * @param DocumentModel $mDocumentModel
     * @return Document
     */
    public static function factory(DocumentModel $mDocumentModel): self
    {
        /** @var Document $mElasticaDocument */
        $mElasticaDocument = self::findOne($mDocumentModel->getId());
        if (!$mElasticaDocument) {
            $mElasticaDocument = new self();
            $mElasticaDocument->set_id($mDocumentModel->getId());
        }

        // поля из проекта, сохраняемые в elasticsearch
        $aProjectAttributes = self::getStoreData($mDocumentModel);
        $mElasticaDocument->setAttributes($aProjectAttributes, false);

        return $mElasticaDocument;
    }

    /**
     * Массив индексируемых данных.
     *
     * @param DocumentModel $mDocumentModel
     * @return array Ключ - поле документа в elasticsearch, значение - значение
     */
    public static function getStoreData(DocumentModel $mDocumentModel): array
    {
        // поля из проекта, сохраняемые в elasticsearch
        $aDocumentAttributes = $mDocumentModel->toArray(self::STORE_FIELDS);

        array_walk($aDocumentAttributes, function ($value) {
            if (!is_string($value)) {
                return $value;
            }

            return trim(preg_replace('/\s+/', ' ', $value));
        });

        return $aDocumentAttributes;
    }

    /**
     * @return array This model's mapping
     */
    public static function mapping(): array
    {
        return [
            'properties' => [
                'id'        => ['type' => 'integer'],
                'name'      => ['type' => 'text', 'analyzer' => 'rebuilt_russian'],
                'keywords'  => ['type' => 'text', 'analyzer' => 'rebuilt_russian'],
                'extension' => ['type' => 'keyword'],
            ],
        ];
    }

    /**
     * @return string the name of the index this record is stored in.
     */
    public static function index(): string
    {
        return Yii::$app->name . '.' . static::type() . '.' . YII_ENV;
    }

    /**
     * Create this model's index
     */
    public static function createIndex(): void
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->createIndex(static::index(), [
            'settings' => [
                "index" => [
                    "number_of_shards"   => 5,
                    "number_of_replicas" => 1,
                    'analysis'           => [
                        'analyzer' => [
                            'rebuilt_russian' => [
                                'tokenizer' => 'standard',
                                'filter'    => ["lowercase", "russian_stemmer", "russian_stop"],
                            ],
                        ],
                        'filter'   => [
                            'russian_stemmer' => [
                                'type'     => 'stemmer',
                                'language' => 'russian',
                            ],
                            'russian_stop'    => [
                                'type'      => 'stop',
                                'stopwords' => '_russian_',
                            ],
                        ],
                    ],
                ],
            ],
            'mappings' => static::mapping(),
            //'warmers' => [ /* ... */ ],
            //'aliases' => [ /* ... */ ],
            //'creation_date' => '...'
        ]);
    }

    /**
     * Delete this model's index
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index());
    }

    /**
     * Список сохраняемых полей в документе elasticsearch
     *
     * @return array|string[]
     */
    public function attributes(): array
    {
        return array_merge(['_id'], self::STORE_FIELDS);
    }
}