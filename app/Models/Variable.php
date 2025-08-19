<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    protected $table = 'rule_variables';
    protected $primaryKey = 'variable_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'variable_id',
        'partner_id',
        'variable_name',
        'description',
        'data_type',
        'source',
    ];
}
