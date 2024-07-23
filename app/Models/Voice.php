<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Voice
 * 
 * @property int $id
 * @property string|null $text
 * @property int $user_id
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
		'user_id' => 'int'
	];

	protected $fillable = [
		'text',
		'user_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
