<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $primaryKey = 'partner_id';

    protected $fillable = [
        'nbfc_name',
        'registration_number',
        'rbi_license_type',
        'date_of_incorporation',
        'business_limit',
        'created_by',
        'updated_by'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'partner_id');
    }

    public function rules()
    {
        return $this->hasMany(Rule::class, 'partner_id');
    }
}
