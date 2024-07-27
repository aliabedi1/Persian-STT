<?php

namespace App\Services;


use App\Exceptions\File\DifficultyCreatingValidationRulesException;
use App\Exceptions\File\EntryFieldsMissMatchTableFillableException;
use App\Exceptions\File\FileException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class FileService
{
    private string $type;

    private string $requestKey = '';

    private string $baseDir = '/';

    /**
     * Name generator for file
     *
     * @param $ext
     * @return string
     */
    private function nameGenerator($ext): string
    {
        return sha1(bcrypt(uniqid() . '.' . microtime())) . '.' . $ext;
    }

    /**
     * Validate type
     *
     * @throws FileException
     */
    private function typeValidation(): void
    {
        if (!isset($this->type) || !in_array($this->type, $this->getTypes()))
            throw new FileException(__('The file type is null or not valid.'));
    }

    /**
     * Get type information from service config's.
     *
     * @param string|null $type_key
     * @return array|null
     */
    public function getTypeFromConfig(string $type_key = null): array|null
    {
        return config('file.types')[$type_key ?? $this->type] ?? null;
    }

    /**
     * Set file type
     *
     * @return array
     */
    private function getTypes(): array
    {
        return array_keys(config('file.types'));
    }

    /**
     * Determine file type for init upload.
     *
     * @param string $type_key
     * @return $this
     */
    public function setType(string $type_key): static
    {
        $this->type = $type_key;
        return $this;
    }

    /**
     * Get base directory
     *
     * @return string
     */
    private function getBaseDir(): string
    {
        return $this->baseDir;
    }

    /**
     * Determine file base directory
     *
     * @param string $base_dir
     * @return $this
     */
    public function setBaseDir(string $base_dir): static
    {
        $this->baseDir = $base_dir;
        return $this;
    }

    /**
     * Validate request key
     *
     * @throws FileException
     */
    private function requestKeyValidation(): void
    {
        if ($this->requestKey === '')
            throw new FileException(__('Request key not set or not valid.'));
    }

    /**
     * Determine file type for init upload.
     *
     * @param string $request_key
     * @return $this
     */
    public function setRequestKey(string $request_key): static
    {
        $this->requestKey = $request_key;
        return $this;
    }

    /**
     * Get file model from type
     *
     * @return Model
     * @throws FileException
     */
    private function getModel(): Model
    {
        $typeConfig = $this->getTypeFromConfig($this->type);
        $model = new $typeConfig["model"];
        if (!($model instanceof Model)) throw new FileException(__('The file sent in the request was not found.'));
        return $model;
    }

    /**
     * Prepare for make validation rule
     *
     * @throws FileException
     */
    public function validationRules(): array
    {
        $this->typeValidation();
        $this->requestKeyValidation();
        return $this->performValidation();
    }

    /**
     * Make validation rule
     *
     * @return array|array[]|string[]
     * @throws DifficultyCreatingValidationRulesException
     */
    private function performValidation(): array
    {
        // get general max_file_size and valid_file_extension
        $max_file_size = 5000; // unit: KB
        $valid_file_extension = 'mp3,wav,aac,flac,ogg,wma,m4a,aiff,aif';
        $required = 'required';

        // validate max_file_size and valid_file_extension
        if (!$max_file_size || !$valid_file_extension)
            throw new DifficultyCreatingValidationRulesException("Error creating validation rules for general files.");

        return [
            $this->requestKey => [
                $required,
                'file',
                'mimes:' . $valid_file_extension,
                'max:' . $max_file_size
            ]
        ];
    }

    /**
     * Prepare file for upload
     *
     * @return array
     * @throws FileException
     */
    public function upload(): array
    {
        $this->typeValidation();
        $this->requestKeyValidation();

        if (!request()->hasFile($this->requestKey))
            throw new FileException(__('The file sent in the request was not found.'));

        return $this->performUpload(request()->file($this->requestKey));
    }

    /**
     * Upload file
     *
     * @param $file
     * @return array
     * @throws FileException
     */
    private function performUpload($file): array
    {
        $typeConfig = $this->getTypeFromConfig($this->type);
        $fileExtension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $fileSize = (int)($file->getSize() / 1024); // unit: KB
        $fileNameFormatted = $this->nameGenerator($fileExtension);

        $file->storeAs($this->baseDir, $fileNameFormatted, ['disk' => $typeConfig["disk"]]);

        // insert to db
        $row = $this
            ->getModel()
            ->create([
                'portal_id' => config('base.portal_id'),
                'language_id' => config('base.language_id'),
                'file' => $fileNameFormatted,
                'file_size' => $fileSize,
                'file_extension' => $fileExtension,
                'userable_type' => auth()->user() ? auth()->user()::class : null,
                'userable_id' => auth()->id() ?? null,
                'created_at' => Carbon::now(),
            ]);

        return [
            'id' => $row->id,
            'name' => $fileNameFormatted,
            'size' => $fileSize,
            'ext' => $fileExtension,
            'url' => $this->getUrl($row->id)
        ];
    }

    /**
     * Update additions fields in file model
     *
     * @param int $fileId
     * @param array $fields
     * @return void
     * @throws EntryFieldsMissMatchTableFillableException
     * @throws FileException
     */
    public function updateOtherFields(int|array $fileId, array $fields): void
    {
        $this->typeValidation();
        if (!$this->checkUpdatableFields($fields))
            throw new EntryFieldsMissMatchTableFillableException('Sent fields not found in the targeted model.');

//        handle array input
        if (is_array($fileId))
            $this->getModel()::query()
                ->whereIn('id', $fileId)
                ->update($fields);
        else
        {
            $fileRow = $this->getModel()::find($fileId);
            if (!$fileRow)
                throw new FileException(__('File not found.'));
            $fileRow->update($fields);
        }

    }

    /**
     * Delete file from disk
     *
     * @param $file_id
     * @return void
     * @throws FileException
     */
    public function deleteFile($file_id): void
    {
        $type = $this->getTypeFromConfig();

        // find file from db
        $fileRow = $this->getModel()->find($file_id);
        if (!$fileRow) throw new FileException(__('File not found.'));

        // remove from disk
        Storage::disk($type["disk"])->delete($fileRow->file);

        // remove from db
        $fileRow->delete();
    }

    /**
     * Get file url
     *
     * @param int $file_id
     * @return string|null
     * @throws FileException
     */
    public function getUrl(int $file_id): null|string
    {
        $typeConfig = $this->getTypeFromConfig($this->type);
        $file = $this->getModel()->find($file_id);
        if (!$file) return null;

        if (!isset($file->is_private))
            throw new FileException(__("Field [is_private] not found in model."));

        if ($file->is_private == IsPrivate::YES) {
            $url = Url::temporarySignedRoute(
                'api.v1.common.files.private',
                now()->addMinutes(10),
                [
                    'type' => $this->type,
                    'file_id' => $file->id,
                    'extension' => $file->file_extension
                ]
            );
        } else {
            $url = Storage::disk($typeConfig["disk"])->url($file->file);
        }

        return $url;
    }

    /**
     * Get file details
     *
     * @param int $file_id
     * @return array|null
     * @throws FileException
     */
    public function getDetails(int $file_id): null|array
    {
        $finalArray = [
            'name' => null,
            'url' => null,
            'size' => null,
            'ext' => null
        ];

        $typeConfig = $this->getTypeFromConfig($this->type);
        $file = $this->getModel()->find($file_id);
        if (!$file) return null;

        $finalArray["size"] = $file->file_size ?? null;
        $finalArray["ext"] = $file->file_extension ?? null;
        $finalArray["name"] = $file->file ?? null;

        if (!isset($file->is_private))
            throw new FileException(__("Field [is_private] not found in model."));

        if ($file->is_private == IsPrivate::YES) {
            $finalArray["url"] = Url::temporarySignedRoute(
                'api.v1.common.files.private',
                now()->addMinutes(10),
                [
                    'type' => $this->type,
                    'file_id' => $file->id,
                    'extension' => $file->file_extension
                ]
            );
        } else {
            $finalArray["url"] = Storage::disk($typeConfig["disk"])->url($file->file);
        }

        return $finalArray;
    }


    /**
     * Checking for updatable fields in file model
     *
     * @param $fields
     * @return bool
     * @throws FileException
     */
    private function checkUpdatableFields($fields): bool
    {
        $model = $this->getModel();
        $modelFillable = $model->getFillable();

        $keys = array_keys($fields);

        return sizeof(array_intersect($modelFillable, $keys)) == sizeof($keys);
    }
}
