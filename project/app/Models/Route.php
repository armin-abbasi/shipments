<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'stop_id',
        'postcode',
        'city',
        'country',
        'type',
    ];

    protected $hidden = ['pivot'];

    public function shipments()
    {
        return $this->belongsToMany(Shipment::class);
    }
}
