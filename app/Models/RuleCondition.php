<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleCondition extends Model
{
    use HasFactory;

    protected $primaryKey = 'condition_id';

    protected $fillable = [
        'rule_id',
        'variable_id',
        'operator',
        'value',
        'created_by',
        'updated_by',
    ];

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id', 'rule_id');
    }

    public function variable()
    {
        return $this->belongsTo(Variable::class, 'variable_id', 'variable_id');
    }
}
