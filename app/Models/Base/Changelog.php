<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Changelog
 * 
 * @property int $id
 * @property int $version_id
 * @property string $type
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Changelog extends Model
{
	protected $table = 'changelog';

	protected $casts = [
		'version_id' => 'int'
	];
}
