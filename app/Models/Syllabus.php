<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Syllabus extends Model
{
    use HasFactory;

    protected $fillable = [
        'syllabus_name',
        'syllabus_file_path',
        'class_id',
        'course_id',
        'session_id'
    ];
}
