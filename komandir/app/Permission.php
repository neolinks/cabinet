<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 11/6/15
 * Time: 2:51 PM
 */

namespace App;

use Zizaco\Entrust\EntrustPermission;
class Permission extends EntrustPermission{
    protected $name;
    protected $display_name;
    protected $description;
} 