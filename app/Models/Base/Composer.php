<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Composer
 * 
 * @property int $id
 * @property string $title
 * @property string|null $title_alternatives
 * @property string $compose_filename
 * @property string $orig_url
 * @property string $orig_compose
 * @property Carbon $orig_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Composer extends Model
{
	protected $table = 'composers';

	protected $casts = [
		'orig_date' => 'date'
	];
}
