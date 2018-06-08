<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Setting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";
}
