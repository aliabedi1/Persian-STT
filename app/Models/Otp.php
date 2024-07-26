<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\IsUsed;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Otp
 * 
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property string $mobile
 * @property string $hashcode
 * @property Carbon $expires_at
 * @property bool $is_used
 * @property Carbon|null $used_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Otp extends Model
{
	use SoftDeletes;
	protected $table = 'otps';

	protected $casts = [
		'user_id' => 'int',
		'expires_at' => 'datetime',
		'is_used' => IsUsed::class,
		'used_at' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'code',
		'hashcode',
		'mobile',
		'expires_at',
		'is_used',
		'used_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
