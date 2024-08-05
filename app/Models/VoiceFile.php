<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\IsPrivate;
use App\Traits\FileUrlGenerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class VoiceFile
 *
 * @property int $id
 * @property string $file
 * @property string $file_size
 * @property string $file_extension
 * @property int $user_id
 * @property bool $is_private
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property User $user
 *
 * @package App\Models
 */
class VoiceFile extends Model
{
    use SoftDeletes;

    protected $table = 'voice_files';

    protected $casts = [
        'user_id' => 'int',
        'is_private' => IsPrivate::class
    ];

    protected $fillable = [
        'file',
        'file_size',
        'file_extension',
        'user_id',
        'is_private'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function voice()
    {
        return $this->belongsTo(Voice::class);
    }
}
