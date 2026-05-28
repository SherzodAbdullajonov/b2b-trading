<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Storage = warehouse / physical stock location.
 *
 * NOTE: The class name "Storage" can collide visually with
 * Illuminate\Support\Facades\Storage. Always reference this model by its full
 * namespace App\Models\Storage in code that also uses the filesystem facade.
 */
class Storage extends Model
{
    use HasFactory;

    protected $table = 'storages';

    protected $fillable = ['name', 'address'];
}
