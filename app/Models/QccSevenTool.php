<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccSevenTool extends Model
{
    protected $table = 'm_qcc_seven_tools';
    protected $fillable = ['tool_name', 'description', 'template_file_name', 'template_file_path'];
}