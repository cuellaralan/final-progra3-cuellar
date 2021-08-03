<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
// extends \Illuminate\Database\Eloquent\Model
class Evento extends \Illuminate\Database\Eloquent\Model
{
    // quitar el updated_at para evitar error de escritura
    public $timestamps = false;
    public $primaryKey = 'id';

    // se usa para borrado logico
    // use SoftDeletes;

    // protected $dates = ['deleted_at'];

    public function users()
    {
        return $this->belongsToMany(Usuario::class);
    }

}