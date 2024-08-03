<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\VoiceStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Voice
 * 
 * @property int $id
 * @property string|null $text
 * @property int $user_id
 * @property int $voice_file_id
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Voice extends Model
{
	use SoftDeletes;
	protected $table = 'voices';

	protected $casts = [
		'user_id' => 'int',
		'voice_file_id' => 'int',
		'status' => VoiceStatus::class
	];

	protected $fillable = [
		'text',
		'user_id',
		'voice_file_id',
		'status',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
    public function voice_file()
    {
        return $this->hasOne(VoiceFile::class);
    }


}
