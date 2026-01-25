## Testing

### Running Tests
- Always use `XDEBUG_MODE=off ./vendor/bin/pest --parallel` for better performance

### Test Structure
- Feature tests in `/tests/Feature/`
- Unit tests in `/tests/Unit/`
- Keep the original folder structure. The test for `app/Livewire/Component` should be located in `/tests/Feature/Livewire/Component`

### Code Style
- Avoid using `beforeEach()` to keep tests isolated
- Use `$this` for Pest methods
- Avoid testing things twice

### Factory Formatting

- **One line** for simple factories:
  ```php
  Team::factory()->create();
  ```
- **Multi-line** when chaining methods:
  ```php
  Team::factory()
      ->count(2)
      ->create();
  ```
- **Attributes in `create()`**, not `factory()`:
  ```php
  // Good
  Team::factory()->create(['name' => 'Test']);
  ShiftSchedule::factory()
      ->recycle($team)
      ->create(['privacy' => Privacy::Team]);

  // Bad
  Team::factory(['name' => 'Test'])->create();
  ```

### Sequence

- **Avoid callbacks** in `sequence()` when not needed
- **Pass models directly** - Laravel resolves them to IDs automatically:
  ```php
  // Good
  ->sequence(
      ['team_id' => $teamA],
      ['team_id' => $teamB],
  )

  // Bad - unnecessary callback and ->id
  ->sequence(
      fn () => ['team_id' => $teamA->id],
      fn () => ['team_id' => $teamB->id],
  )
  ```
  
### Describe Blocks

- Use `describe()` to group related tests
- **One level only** - no nested describe blocks:
  ```php
  // Good
  describe('team privacy', function () {
      it('allows a user to see team shift schedule', function () { });
      it('does not allow access when not in team', function () { });
  });

  // Bad - nested describe
  describe('privacy', function () {
      describe('team', function () {
          it('allows access', function () { });
      });
  });
  ```
