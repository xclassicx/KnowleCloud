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
     * Возвращает модель документа elasticsearch, соответствующую переданному документу.
     * Метод пытается найти документ в elasticsearch, если не находит - создает новый.
     * В любом случае поля модели документа заполняются данными переданной модели.
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
     * Что такое Mapping... Я бы представлял это как выделение типа(ограниченного набора полей) из документа(всех полей).
     * Т.е. из условного "Договора аренды квартиры" можно выделить тип "человек" - по его ФИО, "паспорт" - по номеру и ФИО, "адрес" - по улице квартиры...
     * А так же никто не мешает определить тип "Договор аренды квартиры" и использовать в нем все поля одновременно.
     * Впрочем, никто и не заставляет это делать - все полученные данные в любом будут сохранены и проиндексированы.
     * Т.о. из одного набора переданных в эластику полей - одного документа - можно получить соответствие нескольким Mapping.
     *
     * Пример с договором выше иллюстрирует особенность Mapping - поля ФИО могут участвовать в нескольких типах("человек" и "паспорт"),
     * но храниться будут в одном документе. И удаляться они тоже одновременно.
     * А вот получить 2 соответствия в одном типе из документа - нельзя. Если в примере выше в договоре указан
     * адрес прописки из паспорта И адрес арендуемой квартиры - в тип "адрес" попадет только один из них(явно указанный при создании Mapping).
     *
     * Mapping is the process of defining how a document, and the fields it contains, are stored and indexed.
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html
     *
     * Аналогия с таблицами в SQL - не верна
     * Initially, we spoke about an “index” being similar to a “database” in an SQL database, and a “mapping - type” being equivalent to a “table”.
     * This was a bad analogy that led to incorrect assumptions.
     * In an Elasticsearch index, fields that have the same name in different mapping types are backed by the same Lucene field internally
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/7.17/removal-of-types.html#_why_are_mapping_types_being_removed
     *
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
     * Т.к. один ElasticSearch может обслуживать несколько сайтов и даже один и тот же, но с разными окружениями - используем YII_ENV
     *
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
                            // Дефолтный анализатор русского текста не умел раньше в стоп-слова - указываем тут явно свои
                            'rebuilt_russian' => [
                                // Способ разбивки текста на слова(токены) - по пробелам
                                'tokenizer' => 'standard',
                                // применить к каждому слову(токену) фильтры(можно усложнять синонимами, жаргонизмами их чем еще)
                                'filter'    => ["lowercase", "russian_stemmer", "russian_stop"],
                            ],
                        ],
                        'filter'   => [
                            /**
                             * Как индексировать слова? "Stemmer" - это значит приводить базовой форме и индексировать ее.
                             * Есть несколько способов выделить эту базовую форму:
                             *  - Как в человеческом смысле - использовать в качестве базовой формы лемму(словарная форма):
                             *      - для существительных — именительный падеж, единственное число(кошками → кошка);
                             *      - для прилагательных — именительный падеж, единственное число, мужской род(боязненных → боязненный);
                             *      - для глаголов, причастий, деепричастий — глагол в инфинитиве (неопределённой форме) несовершенного вида(бежал → бежать).
                             * - Так и более "машинных", основанных на отрезании вариативных частей(не всегда морфем целиком и не всегда остается корень слова):
                             *      - кошками → кошка -> "кошк";
                             *      - боязненных → боязненный -> "боязн";
                             *      - бежал → бежать -> "бежа".
                             *      Наглядно видно, что обрезать "до сути" можно различными способами.
                             *
                             * Тут используется дефолтный для ElasticSearch russian stemmer, основанный на отрезании лишнего, как именно - хз
                             */
                            'russian_stemmer' => [
                                'type'     => 'stemmer',
                                'language' => 'russian',
                            ],

                            /**
                             * Список слов, не несущую самостоятельного смысла(в контексте поиска) - всякие предлоги, местоимения и типа того.
                             */
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