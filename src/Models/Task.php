<?php

namespace Ffhs\FfhsTasks\Models;

use App\Models\User;
use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'task_type',
        'task_settings',
        'task_data',
        'finished',
        'deadline_at',
        'start_at',
    ];

    public function getTable()
    {
        return  FfhsTasks::config('table_names.tasks') ?: parent::getTable();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            FfhsTasks::config('table_names.task_user'),
            'task_id',
            'user_id'
        );
    }

    public function taskGroup(): HasMany
    {
        return $this->hasMany(TaskUserGroup::class, 'task_id');
    }

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
            'start_at' => 'datetime',
        ];
    }
}
