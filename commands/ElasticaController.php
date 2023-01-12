<?php

namespace app\commands;

use app\models\Document;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;
use app\models\elasticsearch\Document as ElasticaDocument;

/**
 * Управление поисковым индексом
 */
class ElasticaController extends Controller
{
    const INDEX_ROUTINE_OFFSET = self::class . ':' . 'iIndexRoutineOffset';
    const RUN_ROUTINE_LIMIT = 100;

    /**
     * Первичная индексация документов
     *
     * Используется для разового наполнения индекса существующими документами.
     * После наполнения актуализация происходит при изменении самого документа.
     *
     * @return int
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\elasticsearch\Exception
     */
    public function actionRun(): int
    {
        $aIndices = ElasticaDocument::getDb()->createCommand()->getIndexStats()['indices'];
        if (array_key_exists(ElasticaDocument::index(), $aIndices) === false) {
            ElasticaDocument::createIndex();
            $this->setIndexRoutineOffset(0);
        }

        if (($iIndexRoutineOffset = $this->getIndexRoutineOffset()) !== false) {
            $this->stdout('Indexing...' . PHP_EOL);

            $aDocuments = Document::find()
                ->forCatalog()
                ->orderBy('id')
                ->offset($iIndexRoutineOffset)
                ->limit(self::RUN_ROUTINE_LIMIT)
                ->all();
            foreach ($aDocuments as $mDocument) {
                ElasticaDocument::factory($mDocument)->save();
                $iIndexRoutineOffset++;
            }

            if (count($aDocuments) <= self::RUN_ROUTINE_LIMIT) {
                $this->stdout('Indexing all complete!' . PHP_EOL, BaseConsole::FG_GREEN, BaseConsole::BOLD);
                Yii::$app->getCache()->delete(self::INDEX_ROUTINE_OFFSET);
            } else {
                $this->stdout('Routine done' . PHP_EOL);
                $this->stdout('Run this again for complete indexing all documents' . PHP_EOL, BaseConsole::FG_YELLOW);
                $this->setIndexRoutineOffset($iIndexRoutineOffset);
            }
        }

        return ExitCode::OK;
    }

    /**
     * Убить индекс
     */
    public function actionDrop()
    {
        $this->stdout('Confirm for ' . ElasticaDocument::index() . PHP_EOL, BaseConsole::FG_YELLOW, BaseConsole::BOLD);
        if (!$this->confirm('Delete existing search index(with data, yes)?' . PHP_EOL)) {
            $this->stdout('Canceled', BaseConsole::FG_CYAN);
            return ExitCode::OK;
        }

        ElasticaDocument::deleteIndex();

        return ExitCode::OK;
    }

    protected function getIndexRoutineOffset(): int|false
    {
        return Yii::$app->getCache()->get(self::INDEX_ROUTINE_OFFSET);
    }

    protected function setIndexRoutineOffset(int $iIndexRoutineOffset): bool
    {
        return Yii::$app->getCache()->set(self::INDEX_ROUTINE_OFFSET, $iIndexRoutineOffset, 0);
    }
}
