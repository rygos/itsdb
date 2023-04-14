<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ServersComposersRel
 * 
 * @property int $id
 * @property int $composer_id
 * @property int $server_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ServersComposersRel extends Model
{
	protected $table = 'servers_composers_rel';

	protected $casts = [
		'composer_id' => 'int',
		'server_id' => 'int'
	];
}
