<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
// extends \Illuminate\Database\Eloquent\Model
class Cripto extends \Illuminate\Database\Eloquent\Model
{
    // quitar el updated_at para evitar error de escritura
    // public $timestamps = false;
    // se usa para borrado logico
    // use SoftDeletes;

    // protected $dates = ['deleted_at'];

}