<?php

namespace app\controllers;

use app\models\Document;
use app\models\elasticsearch\Document as ElasticaDocument;
use app\models\elasticsearch\Document as ElasticsearchDocument;
use app\models\forms\Search;
use app\services\Elastica;
use app\services\Flash;
use app\services\Route;
use app\services\WebUser;
use Throwable;
use Yii;
use yii\base\UserException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\imagine\Image;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * DocumentController implements the CRUD actions for Document model.
 */
class DocumentController extends Controller
{
    const NAME = 'document';

    const MAX_RESULT_PAGES = 100;
    const PER_PAGE = 12;

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class'  => AccessControl::class,
                'except' => ['view', 'download', 'search'],
                'rules'  => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     * @see Route::DOCUMENT_SEARCH
     */
    public function actionSearch(): string
    {
        // слишком большой номер страницы может приводить к ошибке запроса Result window is too large
        if ((int)\Yii::$app->request->get('page') > self::MAX_RESULT_PAGES) {
            throw new NotFoundHttpException();
        }

        $dataProvider = false;
        $mSearch = new Search();
        $aQueryParams = \Yii::$app->request->getQueryParams();
        if ($mSearch->load($aQueryParams, '') && $mSearch->validate()) {
            $elasticaUserQuery = ElasticsearchDocument::find()
                ->query([
                    'simple_query_string' => [
                        'fields' => ['name', 'keywords'],
                        'query'  => Elastica::escapeQuery($mSearch->q),
                    ],
                ]);
            $dataProvider = new ActiveDataProvider([
                'query'      => $elasticaUserQuery,
                'pagination' => [
                    'pageSize'        => self::PER_PAGE,
                    'defaultPageSize' => self::PER_PAGE,
                ],
            ]);
        }

        return $this->render('search', [
            'dataProvider' => $dataProvider,
            'mSearch'      => $mSearch,
        ]);
    }

    /**
     * Lists all auth user Document models.
     *
     * @return string
     * @see Route::DOCUMENT_MY
     */
    public function actionMy(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Document::find()->whereOwner(WebUser::getAuthUser()),

            'pagination' => [
                'pageSize' => self::PER_PAGE,
            ],
        ]);

        return $this->render('my', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Document model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @see Route::DOCUMENT_CREATE
     */
    public function actionCreate(): Response|string
    {
        $mDocument = new Document();
        $mDocument->setScenario(Document::SCENARIO_CREATE);
        $mDocument->setOwner(WebUser::getAuthUser());

        $request = Yii::$app->getRequest();
        if (!$request->isPost) {
            return $this->render('create', [
                    'mDocument' => $mDocument,
                ]
            );
        }

        $mDocument->setUploadedFile(UploadedFile::getInstance($mDocument, 'uploadedFile'));
        if (!$mDocument->load($request->post()) || !$mDocument->validate()) {
            return $this->render('create', [
                    'mDocument' => $mDocument,
                ]
            );
        }

        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            if (!$mDocument->save()) {
                throw new UserException(implode($mDocument->getFirstErrors()));
            }

            if (!$mDocument->uploadedFile->saveAs($mDocument->getFilePath())) {
                throw new UserException('Ошибка сохранения файла');
            }

            if ($this->createPreview($mDocument) === false) {
                throw new UserException('Ошибка создания превью');
            }

            if ($mDocument->isPublic()) {
                ElasticaDocument::factory($mDocument)->save();
            }
        } catch (Throwable $ex) {
            $transaction->rollBack();

            if (file_exists($mDocument->getFilePath())) {
                unlink($mDocument->getFilePath());
            }
            if ($mDocument->hasPreview() && file_exists($mDocument->getPreviewPath())) {
                unlink($mDocument->getPreviewPath());
            }

            Yii::error($ex->getMessage());
            $message = 'Ошибка создания документа';
            if ($ex instanceof UserException) {
                $message = $ex->getMessage();
            }
            Flash::addDanger($message);

            return $this->render('create', [
                    'mDocument' => $mDocument,
                ]
            );
        }
        $transaction->commit();

        Flash::addSuccess('Документ создан');
        return $this->redirect([Route::DOCUMENT_VIEW, 'iDocumentId' => $mDocument->getId()]);
    }

    /**
     * Displays a single Document model
     *
     * @see Route::DOCUMENT_VIEW
     */
    public function actionView(int $iDocumentId, string $key = ''): string
    {
        $mDocument = $this->findModel($iDocumentId);
        if (!($mDocument->checkAccess($key) || $mDocument->isOwner(WebUser::getAuthUser()))) {
            throw new ForbiddenHttpException();
        }

        return $this->render('view', [
            'mDocument' => $mDocument,
        ]);
    }

    /**
     * @see Route::DOCUMENT_DOWNLOAD
     */
    public function actionDownload(int $iDocumentId, string $key = ''): Response
    {
        $mDocument = $this->findModel($iDocumentId);
        if (!($mDocument->checkAccess($key) || $mDocument->isOwner(WebUser::getAuthUser()))) {
            throw new ForbiddenHttpException();
        }

        return Yii::$app->response->sendFile(
            $mDocument->getFilePath(),
            $mDocument->getFilename() . '.' . $mDocument->getFileExtension()
        );
    }

    /**
     * Updates an existing Document model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @see Route::DOCUMENT_UPDATE
     */
    public function actionUpdate(int $iDocumentId): Response|string
    {
        $mDocument = $this->findModel($iDocumentId);
        if ($mDocument->isOwner(WebUser::getAuthUser()) === false) {
            throw new ForbiddenHttpException();
        }

        $request = Yii::$app->getRequest();
        if (
            $request->isPost === false
            || $mDocument->load($request->post()) === false
            || $mDocument->save() === false
        ) {
            return $this->render('update', [
                'mDocument' => $mDocument,
            ]);
        }
        if ($mDocument->isPublic()) {
            ElasticaDocument::factory($mDocument)->save();
        } else {
            ElasticaDocument::factory($mDocument)->delete();
        }

        Flash::addSuccess('Документ обновлен');
        return $this->redirect([Route::DOCUMENT_VIEW, 'iDocumentId' => $mDocument->getId()]);
    }

    /**
     * Deletes an existing Document model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @see Route::DOCUMENT_DELETE
     */
    public function actionDelete(int $iDocumentId): Response
    {
        $mDocument = $this->findModel($iDocumentId);
        if ($mDocument->isOwner(WebUser::getAuthUser()) === false) {
            throw new ForbiddenHttpException();
        }

        $sFilePath = $mDocument->getFilePath();
        $hasPreview = $mDocument->hasPreview();
        $sPreviewPath = $mDocument->getPreviewPath();
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            // Именно такой порядок удаления "модель -> превью -> файл" позволяет восстановить все как было, при возникновении ошибки
            if (!$mDocument->delete()) {
                throw new UserException(implode($mDocument->getFirstErrors()));
            }

            if ($hasPreview && unlink($sPreviewPath) === false) {
                throw new UserException('Ошибка удаления превью');
            }

            if (unlink($sFilePath) === false) {
                throw new UserException('Ошибка удаления файла');
            }
        } catch (Throwable $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());

            if ($hasPreview && file_exists($sPreviewPath) === false) {
                if ($this->createPreview($mDocument) === false) {
                    Yii::error('Не смог восстановить превью');
                }
            }

            $message = 'Ошибка удаления документа';
            if ($ex instanceof UserException) {
                $message = $ex->getMessage();
            }
            Flash::addDanger($message);

            return $this->redirect([Route::DOCUMENT_VIEW, 'iDocumentId' => $mDocument->getId()]);
        }
        $transaction->commit();

        return $this->redirect([Route::ROOT]);
    }

    /**
     * Finds the Document model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Document
    {
        if (($model = Document::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function createPreview(Document $mDocument): bool
    {
        $sPreviewPath = $mDocument->getPreviewPath();
        if (!$sPreviewPath) {
            return true;
        }

        Image::thumbnail(
            $mDocument->getFilePath(),
            Document::PREVIEW_WIDTH, Document::PREVIEW_HEIGHT)
            ->save($sPreviewPath);

        return file_exists($sPreviewPath);
    }
}
