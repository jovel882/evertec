<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
    * Relacion con las ordenes.
    *
    * @return Relacion.
    */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
    public function getPermissions()
    {
        if (!\Session::has('permissions')) {
            \Session::put('permissions', $this->getAllPermissions()->pluck('name')->toArray());
        }

        return \Session::get('permissions');
    }
    public function getRoles()
    {
        if (! \Session::has('roles')) {
            \Session::put('roles', $this->getRoleNames()->toArray());
        }

        return \Session::get('roles');
    }
}
