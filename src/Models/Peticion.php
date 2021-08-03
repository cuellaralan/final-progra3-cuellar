<?php
//ar -> manejador
namespace App\Models;

use DateTime;

class Peticion
{
    public $fecha;
    public $hora;
    public $ruta;

    public function __construct($fechaN, $horaN, $rutaN)
    {
        $this->fecha = $fechaN;
        $this->hora = $horaN;
        $this->ruta = $rutaN;
    }
    
    public static function Ordernar($array)
    {
        usort($array, 'compara_fecha');
        return $array;
    }

    public function compara_fecha($a, $b){
        return strtotime(trim($a['fecha'])) > strtotime(trim($b['fecha']));
     }
}