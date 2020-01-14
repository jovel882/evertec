<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];    
    protected $guarded = ['id'];

    /**
    * Relacion con las transacciones.
    *
    * @return Relacion.
    */
    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }
    /**
    * Relacion con el usuario.
    *
    * @return Relacion.
    */    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    /**
    * Scope de la consulta para obtener solo las ordenes propias del usuario.
    *
    * @param  \Illuminate\Database\Eloquent\Builder  $query Consulta
    * @return \Illuminate\Database\Eloquent\Builder Consulta.
    */
    public function scopeOwn($query)
    {
        return $query->where('user_id', auth()->user()->id??null);
    }        
}
