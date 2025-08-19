<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'partner_id',
        'product_name',
        'product_type',
        'min_amount',
        'max_amount',
        'min_tenure',
        'max_tenure',
        'interest_rate',
        'created_by',
        'updated_by'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function rules()
    {
        return $this->hasMany(Rule::class, 'product_id');
    }
}
