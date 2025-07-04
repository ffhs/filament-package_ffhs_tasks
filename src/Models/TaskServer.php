<?php

namespace Ffhs\FfhsTasks\Models;

use App\Models\User;
use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskServer extends Model
{
    protected $fillable = [
        'title',
        'url',
        'token',
    ];

    protected $hidden = ['token'];

    public function getTable()
    {
        return  FfhsTasks::config('table_names.task_servers') ?: parent::getTable();
    }

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
        ];
    }


}
