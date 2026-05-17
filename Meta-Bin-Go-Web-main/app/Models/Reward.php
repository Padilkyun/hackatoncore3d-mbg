<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'nama_reward',
        'syarat_point',
        'kuota',
        'foto_reward',
        'keterangan',
        'status',
    ];
}
