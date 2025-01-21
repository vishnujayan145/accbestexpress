<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class IncomeExpenseGroup extends Model
{
    use Notifiable;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
        'deleted_by'

    ];
    protected $dates = ['deleted_at'];
    public function IncomeExpenseHeads()
    {
        return $this->hasMany('App\IncomeExpenseHead', 'income_expense_group_id');
    }
}
