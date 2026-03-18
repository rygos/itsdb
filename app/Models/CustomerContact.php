<?php

namespace App\Models;

use App\Models\Base\CustomerContact as BaseCustomerContact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
