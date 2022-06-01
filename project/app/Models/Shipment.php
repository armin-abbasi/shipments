<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'distance',
        'time',
        'price',
        'company_id',
        'carrier_id',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class);
    }
}
