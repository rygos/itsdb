<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ComposerContainerRel
 * 
 * @property int $id
 * @property int $composer_id
 * @property int $container_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ComposerContainerRel extends Model
{
	protected $table = 'composer_container_rel';

	protected $casts = [
		'composer_id' => 'int',
		'container_id' => 'int'
	];
}
