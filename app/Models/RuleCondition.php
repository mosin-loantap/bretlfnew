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
        'variable_name',
        'operator',
        'value',
        'marks',
        'is_mandatory',
        'created_by',
        'updated_by',
    ];

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id', 'rule_id');
    }

    /**
     * Get the variable data type from the variables table
     * This is optional - for validation purposes
     */
    public function getVariableDataType()
    {
        $variable = Variable::where('variable_name', $this->variable_name)->first();
        return $variable ? $variable->data_type : 'string';
    }
}
