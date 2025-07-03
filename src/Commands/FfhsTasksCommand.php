<?php

namespace Ffhs\FfhsTasks\Commands;

use Illuminate\Console\Command;

class FfhsTasksCommand extends Command
{
    public $signature = 'filament-package_ffhs_tasks';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
