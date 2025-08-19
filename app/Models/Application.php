<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $primaryKey = 'application_id';

    protected $fillable = [
        'partner_id',
        'product_id',
        'customer_name',
        'requested_amount',
        'requested_tenure',
        'status',
        'created_by',
        'updated_by'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
