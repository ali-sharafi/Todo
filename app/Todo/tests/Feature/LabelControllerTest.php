<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Constraint\Constraint;
use Tests\TestCase;
use Todo\Model\Label;
use Todo\Model\LabelTask;
use Todo\Model\Task;

class LabelControllerTest extends TestCase
{

    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_name_field_required()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson('/labels', []);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_can_add_label()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson('/labels', [
            'name' => 'test'
        ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function test_can_not_add_duplicate_label()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson('/labels', [
            'name' => 'test'
        ]);

        $response->assertStatus(201);

        $response = $this->postJson('/labels', [
            'name' => 'test'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_can_get_labels()
    {
        $this->withoutExceptionHandling();
        $task = factory(Task::class)->create(['user_id' => $this->user->id]);
        $labels = factory(Label::class)->create();

        $task->labels()->sync($labels->pluck('id')->toArray());

        $task = factory(Task::class)->create(['user_id' => factory(User::class)->create()->id]);

        $task->labels()->sync($labels->pluck('id')->toArray());

        $response = $this->getJson('/labels');

        $response->assertJsonCount(1);

        $this->assertThat($response->getData()->data, $this->labelHasTaskCount(1));
    }

    private function labelHasTaskCount($count): Constraint
    {
        return new class($count) extends Constraint
        {
            private $count;

            public function __construct($count)
            {
                $this->count = $count;
            }

            protected function matches($labels): bool
            {
                foreach ($labels as $label) {
                    if ($label->tasks_count === $this->count) {
                        return true;
                    }
                }

                return false;
            }

            public function toString(): string
            {
                return sprintf('Task count not matched "%s"', $this->count);
            }
        };
    }
}
