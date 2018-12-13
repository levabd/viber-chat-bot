<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{

    protected $table = 'drugs';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name'
    ];
}
