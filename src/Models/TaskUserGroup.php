<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TaskUserGroup extends Model
{
    protected $fillable = [
      'task_id',
      'user_group_id',
      'user_group_type'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function userGroup(): MorphTo
    {
        return $this->morphTo('user_group');
    }

    public function getTable()
    {
        return  FfhsTasks::config('table_names.task_user_group') ?: parent::getTable();
    }
}
