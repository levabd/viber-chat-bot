<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViberUser extends Model
{

    protected $table = 'viber_users';

    protected $fillable = [
        'viber_id',
        'session_id',
        'completed_session_id',
        'name',
        'subscribed'
    ];

    protected $casts = [
        'viber_id' => 'string',
        'name' => 'string',
        'subscribed' => 'boolean'
    ];

    public function session()
    {
        return $this->belongsTo('App\Models\Session', 'session_id', 'id');
    }

    public function completedSession()
    {
        return $this->belongsTo('App\Models\Session', 'completed_session_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany('App\Models\Session', 'user_id', 'id');
    }
}
