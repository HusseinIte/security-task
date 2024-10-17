<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ExceptionLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exception_type',
        'message',
        'status_code',
        'file',
        'line',
        'stack_trace',
    ];
    protected $table = 'exceptions';
}
