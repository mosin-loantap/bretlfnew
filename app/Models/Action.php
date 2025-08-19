<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $table = 'rule_actions';
    protected $primaryKey = 'action_id';

    protected $fillable = [
        'rule_id',
        'action_type',
        'parameters',
        'created_by',
        'updated_by'
    ];

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }
}
