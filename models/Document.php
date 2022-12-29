<?php

namespace app\models;

use app\migrations\Tables;
use app\services\DbDate;
use DateTime;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "document".
 *
 * @property int $id
 * @property int $owner
 * @property string $name
 * @property string $keywords
 * @property string $filename
 * @property string $file_extension
 * @property string|null $file_mime
 * @property string $created
 * @property bool $public
 */
class Document extends ActiveRecord
{
    public const SCENARIO_CREATE = 'scenario_create';
    public const AVAILABLE_EXTENSIONS = ['png', 'jpeg', 'jpg', 'zip'];
    public const MAX_SIZE_MB = 10;
    public const PREVIEW_WIDTH = 360;
    public const PREVIEW_HEIGHT = 240;
    public const PREVIEWS_DIR = 'previews';
    public const UPLOADS_DIR = 'uploads';

    private const ACCESS_KEY_SALT = 'ulf#6#?K8pCu#I27';

    /**
     * Сюда помещается только что загруженный файл, для проверок в валидаторах
     *
     * @var \yii\web\UploadedFile|null
     */
    public ?UploadedFile $uploadedFile = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return Tables::DOCUMENT;
    }

    /**
     * @inheritdoc
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($scenarios[self::SCENARIO_DEFAULT], ['!uploadedFile']);
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['owner', 'name', 'keywords', 'filename', 'file_extension', 'public'], 'required'],
            [['owner'], 'integer'],
            [['created'], 'safe'],
            [['public'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['keywords'], 'string', 'max' => 1024],
            [['filename'], 'string', 'max' => 32],
            [['file_extension'], 'string', 'max' => 16],
            [['file_mime'], 'string', 'max' => 64],
            [['uploadedFile'], 'required', 'on' => self::SCENARIO_CREATE],
            [['uploadedFile'],
                'file',
                'skipOnEmpty' => false,
                'extensions'  => self::AVAILABLE_EXTENSIONS,
                'maxSize'     => self::MAX_SIZE_MB * 1024 * 1024,
                'on'          => self::SCENARIO_CREATE,
            ],
            [['owner'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['owner' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'             => 'ID',
            'owner'          => 'Owner',
            'name'           => 'Название документа',
            'keywords'       => 'Ключевые слова',
            'filename'       => 'Filename',
            'file_extension' => 'File Extension',
            'file_mime'      => 'File Mime',
            'created'        => 'Created',
            'public'         => 'Показывать в поиске',
            'uploadedFile'   => 'Загружаемый файл',
        ];
    }

    public function isOwner(?Account $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->getId() === $this->owner;
    }

    public function getOwner(): Account
    {
        /** @var \app\models\Account $owner */
        $owner = $this->hasOne(Account::class, ['id' => 'owner'])->one();
        return $owner;
    }

    public function setOwner(Account $owner): self
    {
        $this->owner = $owner->getId();
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getFileExtension(): string
    {
        return $this->file_extension;
    }

    public function setFileExtension(string $file_extension): self
    {
        $this->file_extension = $file_extension;
        return $this;
    }

    public function getFileMime(): ?string
    {
        return $this->file_mime;
    }

    public function setFileMime(?string $file_mime): self
    {
        $this->file_mime = $file_mime;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return DbDate::fromDatabase($this->created);
    }

    public function checkAccess(string $sKey): bool
    {
        if ($this->public) {
            return true;
        }

        return $sKey === $this->getAccessKey();
    }

    public function getAccessKey(): string
    {
        return md5($this->id . self::ACCESS_KEY_SALT);
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function makePublic(): self
    {
        $this->public = true;
        return $this;
    }

    public function makeProtected(): self
    {
        $this->public = false;
        return $this;
    }

    public function getFilePath(): string
    {
        return realpath(Yii::getAlias('@app') . '/' . self::UPLOADS_DIR) . '/' . $this->getFilename();
    }

    public function hasPreview(): bool
    {
        return in_array($this->file_extension, ['png', 'jpeg', 'jpg']);
    }

    public function getPreviewPath(): ?string
    {
        if (!$this->hasPreview()) {
            return null;
        }

        return realpath(Yii::getAlias('@webroot') . '/' . self::PREVIEWS_DIR) . '/' . $this->getFilename() . '.' . $this->file_extension;
    }

    public function setUploadedFile(UploadedFile $file): self
    {
        $this->uploadedFile = $file;

        // Теоретически этот код не исключает коллизию имен файлов. Но практически пренебрежимы
        $this->filename = substr(md5(mt_rand()), 0, 2) . strrev(time());
        $this->file_extension = $file->getExtension();
        $this->file_mime = $file->type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function find(): DocumentQuery
    {
        return new DocumentQuery(get_called_class());
    }
}
