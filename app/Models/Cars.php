<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/10/15
 * Time: 4:29 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Cars extends Model{
    protected $table = 'cars';
    protected $fillable = ['*'];
    protected $timestamp = false;
} 