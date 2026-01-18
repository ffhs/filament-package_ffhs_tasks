<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Database\Factories\TaskServerFactory;
use Ffhs\FfhsTasks\Traits\IsFfhsTaskModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskServer extends Model
{
    /** @use HasFactory<TaskServerFactory> */
    use HasFactory;
    use IsFfhsTaskModel;

    protected $fillable = [
        'title',
        'url',
        'token',
    ];

    protected $hidden = ['token'];

    protected static string $factory = TaskServerFactory::class;

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
