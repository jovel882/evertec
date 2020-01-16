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
    * Accesor para el nombre del usuario.
    *
    * @return string Nombre.
    */    
    public function getNameUserAttribute()
    {
        return $this->user->name;
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

    /**
     * Almacena un nuevo registro
     * 
     * @param  array $data Datos para almacenar la orden.
     * @return Order|false Modelo con la orden nueva o un estado false si hay algun error.
     */
    public function store($data){
        try {
            return $this->create($data);
        } catch (\Illuminate\Database\QueryException $exception) {
            return false;  
        }
    }
    
    /**
     * Obtiene una orden por el id requerido.
     * 
     * @param integer $id Id de la orden a buscar.
     * @param boolean $withTrash Indica si la busqueda se debe hacer con registros en la papelera o no.
     * @return Order Modelo.
     */
    public function getById($id,$withTrash=false){
        $query = $this->with(["transactions" => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->where("id", $id);
        if($withTrash){
            $query->withTrashed();
        }
        return $query->first();
    }
    
    /**
     * Obtiene todas las ordenes.
     * 
     * @param boolean $withTrash Indica si la busqueda se debe hacer con registros en la papelera o no, junto con unicamente los propios o todos.
     * @return Collection Coleccion con los modelos encontrados.
     */    
    public function getAll($withTrash=false){
        $query = $this->with("user");
        if($withTrash){
            $query = $query->withTrashed();
        } else {
            $query = $query->own();
        }
        return $query->get();
    }    

    /**
     * Obtiene la ultima transaccion de la orden.
     *      
     * @return Transaction Modelo con la transaccion.
     */    
    public function getLastTransaction(){
        return $this->transactions()
            ->orderBy('created_at', 'desc')->first();        
    }    
}
