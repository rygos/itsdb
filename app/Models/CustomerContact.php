<?php

namespace App\Models;

use App\Models\Base\CustomerContact as BaseCustomerContact;

class CustomerContact extends BaseCustomerContact
{
	protected $fillable = [
		'customer_id',
		'prefix',
		'name',
		'familyname',
		'phone_mobile',
		'phone_office',
		'email',
		'comments'
	];

    public function customer(){
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }
}
