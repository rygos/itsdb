<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChangelogVersion
 * 
 * @property int $id
 * @property string $version
 * @property string|null $description
 * @property int $published
 * @property Carbon $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ChangelogVersion extends Model
{
	protected $table = 'changelog_versions';

	protected $casts = [
		'published' => 'int',
		'published_at' => 'datetime'
	];
}
