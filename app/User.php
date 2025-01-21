<?php

namespace App;

use App\Language;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_manage_id',
        'language_id',
        'create_by',
        'update_by',
        'delete_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    public function profile()
    {
        return $this->hasOne('App\Profile');
    }
    public function role()
    {
        return $this->hasOne('App\RoleManage', 'id');
    }

    /**
     * This function return assaign language 
     *
     * @author      Md. Al-Mahmud <mamun120520@gmail.com>
     * @version     1.0
     * @see         
     * @since       10/01/2022
     * Time         13:02:46
     * @param       
     * @return      
     */
    public function language()
    {
        # code...   
        return $this->belongsTo(Language::class, 'language_id');
    }
    #end

}
