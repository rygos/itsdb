<?php

namespace App\Models;

use App\Models\Base\Remark as BaseRemark;

class Remark extends BaseRemark
{
	protected $fillable = [
		'type',
		'relation_id',
		'remark'
	];
}
