<?php

namespace app\services;

use app\models\Document as DocumentModel;
use yii\helpers\Url;

class Document
{
    const ICONS_DIR = 'file-icons';

    public static function getPreviewUrl(DocumentModel $document): string
    {
        if ($document->hasPreview()) {
            return '/' . DocumentModel::PREVIEWS_DIR . '/' . $document->getFilename() . '.' . $document->getFileExtension();
        }

        return '/' . self::ICONS_DIR . '/' . $document->getFileExtension() . '.svg';
    }

    public static function getViewUrl(DocumentModel $document, bool $isAbsolute = false): string
    {
        $aRoute = [Route::DOCUMENT_VIEW, 'iDocumentId' => $document->getId()];
        if ($document->isPublic() === false) {
            $aRoute['key'] = $document->getAccessKey();
        }

        return Url::toRoute($aRoute, $isAbsolute);
    }

    public static function getDownloadUrl(DocumentModel $document, bool $isAbsolute = false): string
    {
        $aRoute = [Route::DOCUMENT_DOWNLOAD, 'iDocumentId' => $document->getId()];
        if ($document->isPublic() === false) {
            $aRoute['key'] = $document->getAccessKey();
        }

        return Url::toRoute($aRoute, $isAbsolute);
    }
}