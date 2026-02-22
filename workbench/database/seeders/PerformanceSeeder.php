<?php

namespace Database\Seeders;

use App\Models\FirstUserGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PerformanceSeeder extends Seeder
{
    private const BATCH_SIZE = 500;

    private const TASKS_PER_CATEGORY = 50_000;

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Performance User',
            'email' => 'perf@ffhs.ch',
            'password' => Hash::make('password'),
        ]);

        $group = FirstUserGroup::factory()->create();
        $group->users()->attach($user);

        $userMorphType = $user->getMorphClass();
        $groupMorphType = $group->getMorphClass();

        $this->command->info('Seeding 50k public tasks (unassigned)...');
        $this->seedPublicTasks();

        $this->command->info('Seeding 50k private tasks (creator = user)...');
        $this->seedCreatorTasks($user->getKey(), $userMorphType);

        $this->command->info('Seeding 50k private tasks (assigned to user)...');
        $this->seedAssignedTasks($user->getKey(), $userMorphType);

        $this->command->info('Seeding 50k private tasks (assigned to group)...');
        $this->seedGroupAssignedTasks($group->getKey(), $groupMorphType);

        $this->command->info('Done. 200k tasks seeded.');
    }

    private function seedPublicTasks(): void
    {
        $this->insertTasksInBatches(fn (int $i) => $this->buildTask($i, 'public'));
    }

    private function seedCreatorTasks(int $userId, string $userMorphType): void
    {
        $this->insertTasksInBatches(
            fn (int $i) => $this->buildTask($i, 'private', $userMorphType, $userId),
        );
    }

    private function seedAssignedTasks(int $userId, string $userMorphType): void
    {
        $taskIds = $this->insertTasksInBatches(
            fn (int $i) => $this->buildTask($i, 'private'),
            returnIds: true,
        );

        $this->command->info('  Inserting assignable pivots...');
        $now = Carbon::now();

        foreach (array_chunk($taskIds, self::BATCH_SIZE) as $chunk) {
            $pivots = array_map(fn (int $taskId) => [
                'task_id' => $taskId,
                'assignable_type' => $userMorphType,
                'assignable_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk);

            DB::table(config('ffhs-tasks.tables.task_assignables'))->insert($pivots);
        }
    }

    private function seedGroupAssignedTasks(int $groupId, string $groupMorphType): void
    {
        $taskIds = $this->insertTasksInBatches(
            fn (int $i) => $this->buildTask($i, 'private'),
            returnIds: true,
        );

        $this->command->info('  Inserting assignable pivots...');
        $now = Carbon::now();

        foreach (array_chunk($taskIds, self::BATCH_SIZE) as $chunk) {
            $pivots = array_map(fn (int $taskId) => [
                'task_id' => $taskId,
                'assignable_type' => $groupMorphType,
                'assignable_id' => $groupId,
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk);

            DB::table(config('ffhs-tasks.tables.task_assignables'))->insert($pivots);
        }
    }

    /**
     * @return list<int>|void
     */
    private function insertTasksInBatches(callable $rowBuilder, bool $returnIds = false): ?array
    {
        $table = config('ffhs-tasks.tables.tasks');
        $taskIds = $returnIds ? [] : null;

        for ($offset = 0; $offset < self::TASKS_PER_CATEGORY; $offset += self::BATCH_SIZE) {
            $rows = [];
            $batchEnd = min($offset + self::BATCH_SIZE, self::TASKS_PER_CATEGORY);

            for ($i = $offset; $i < $batchEnd; $i++) {
                $rows[] = $rowBuilder($i);
            }

            if ($returnIds) {
                $firstId = DB::table($table)->insertGetId($rows[0]);
                if (count($rows) > 1) {
                    DB::table($table)->insert(array_slice($rows, 1));
                }

                $count = count($rows);
                for ($j = 0; $j < $count; $j++) {
                    $taskIds[] = $firstId + $j;
                }
            } else {
                DB::table($table)->insert($rows);
            }
        }

        return $taskIds;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTask(
        int $index,
        string $privacy,
        ?string $creatorType = null,
        ?int $creatorId = null,
    ): array {
        $now = Carbon::now();

        return [
            'title' => "Task {$privacy} #{$index}",
            'description' => 'Performance test task',
            'type' => 'default',
            'status' => TaskStatus::InProgress->value,
            'privacy' => $privacy,
            'creator_type' => $creatorType,
            'creator_id' => $creatorId,
            'can_be_cancelled' => false,
            'cancelled_at' => null,
            'completed_at' => null,
            'starts_at' => $now->subDays(rand(1, 30)),
            'deadline_at' => $now->copy()->addDays(rand(1, 30)),
            'extra' => null,
            'data' => null,
            'deleted_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
