<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $primaryKey = 'rule_id';

    protected $fillable = [
        'partner_id',
        'product_id',
        'rule_name',
        'rule_type',
        'priority',
        'effective_from',
        'effective_to',
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

    public function conditions()
    {
        return $this->hasMany(RuleCondition::class, 'rule_id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class, 'rule_id');
    }
}
