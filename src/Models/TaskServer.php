<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Traits\IsFfhsTaskModel;
use Illuminate\Database\Eloquent\Model;

class TaskServer extends Model
{
    use IsFfhsTaskModel;

    protected $fillable = [
        'title',
        'url',
        'token',
    ];

    protected $hidden = ['token'];

    protected static function configKey(): string
    {
        return 'task_servers';
    }

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
        ];
    }
}
