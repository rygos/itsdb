<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Server
 * 
 * @property int $id
 * @property string|null $servername
 * @property string|null $fqdn
 * @property string|null $ext_ip
 * @property string|null $int_ip
 * @property string|null $db_sid
 * @property string|null $db_server
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $customer_id
 * @property int $user_id
 * @property string|null $docker_compose_raw
 * @property string|null $env_raw
 * @property string|null $server_cert_raw
 * @property string|null $type
 * @property int|null $server_kind_id
 * @property int|null $operating_system_id
 * @property string|null $private_key_raw
 * @property int $cert_server_ok
 * @property int $cert_intermediate_ok
 * @property int $cert_root_ok
 * @property int $cert_key_ok
 *
 * @package App\Models\Base
 */
class Server extends Model
{
	protected $table = 'servers';

	protected $casts = [
		'customer_id' => 'int',
		'user_id' => 'int',
		'server_kind_id' => 'int',
		'operating_system_id' => 'int',
		'cert_server_ok' => 'int',
		'cert_intermediate_ok' => 'int',
		'cert_root_ok' => 'int',
		'cert_key_ok' => 'int'
	];
}
