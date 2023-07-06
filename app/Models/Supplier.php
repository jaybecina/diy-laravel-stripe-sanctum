<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company',
        'status',
        'created_by',
    ];

    /**
     * The moodboard that belong to the gallery.
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'supplier_id', 'id');
    }
}
