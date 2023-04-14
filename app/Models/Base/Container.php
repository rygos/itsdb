<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Container
 * 
 * @property int $id
 * @property string $title
 * @property string|null $content
 * @property string $content_orig
 * @property Carbon $content_orig_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Container extends Model
{
	protected $table = 'containers';

	protected $casts = [
		'content_orig_date' => 'date'
	];
}
