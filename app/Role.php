<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 11/6/15
 * Time: 2:50 PM
 */

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole{
    protected $name;
    protected $display_name;
    protected $description;
} 