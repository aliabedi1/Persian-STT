<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * 
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $mobile
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Otp[] $otps
 * @property Collection|VoiceFile[] $voice_files
 * @property Collection|Voice[] $voices
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens;
	use SoftDeletes;
	protected $table = 'users';

	protected $fillable = [
		'first_name',
		'last_name',
		'mobile'
	];

	public function otps()
	{
		return $this->hasMany(Otp::class);
	}

	public function voice_files()
	{
		return $this->hasMany(VoiceFile::class);
	}

	public function voices()
	{
		return $this->hasMany(Voice::class);
	}
}
