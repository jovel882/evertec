<?php
namespace App\Helpers;
class CustomHelpers {
    /**
     * Inicia el registro de log de consultas.
     * @return void.
     */    
    static public function initDBLog()
    {
        \DB::enableQueryLog();
    }
    /**
     * Retorna todo el log de las consultas
     * @return array Con todas las consultas realizadas.
     */    
    static public function getDBLog()
    {
        return \DB::getQueryLog();
    }  
    /**
     * Retorna la consulta formateada.
     * @param query $query Consulta.
     * @return string Texto full de la consulta.
     */          
    static public function getSqlFull($query)
    {
        return str_replace_array('?', collect($query->getBindings())->map(function ($binding) {
            return is_numeric($binding) ? $binding : "'".addslashes($binding)."'";
        })->toArray(), $query->toSql());
    }                      
}