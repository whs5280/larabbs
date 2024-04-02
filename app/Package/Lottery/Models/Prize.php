<?php

namespace App\Package\Lottery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;;

/**
 * @property mixed $name
 */
class Prize extends Model
{
    use HasFactory;

    protected $table = 'l_prizes';
}
