<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{

    protected $table = 'sessions';

    protected $fillable = [
        'user_id',
        'last_message_id',
        'drug_id',
        'stage_num',
        'procedure_at'
    ];

    protected $dates = [
        'procedure_at'
    ];

    protected $casts = [
        'user_id' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\ViberUser', 'user_id', 'id');
    }

    public function drug()
    {
        return $this->belongsTo('App\Models\Drug', 'drug_id', 'id');
    }
}
