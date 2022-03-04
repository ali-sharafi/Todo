<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Todo\Http\Resources\TaskCollection;
use Todo\Http\Resources\TaskResource;
use Todo\Model\Label;
use Todo\Model\Task;
use Todo\Notifications\TaskClosed;

class TaskControllerTest extends TestCase
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
    public function test_title_is_required()
    {
        $response = $this->postJson('/tasks', []);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_can_add_task()
    {
        $task = factory(Task::class)->make()->toArray();

        $response = $this->postJson('/tasks', $task);

        $response->assertStatus(201);
    }

    /** @test */
    public function test_can_update_task()
    {
        $task = factory(Task::class)->create(['user_id' => $this->user->id]);

        $response = $this->patch('/tasks/' . $task->id, [
            'title' => 'test',
            'description' => 'test'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', ['title' => 'test']);
    }

    /** @test */
    public function test_can_update_task_status()
    {
        $task = factory(Task::class)->create(['user_id' => $this->user->id]);

        $response = $this->patch('/tasks/' . $task->id . '/status');

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', ['status' => $task->status]);

        $this->assertDatabaseHas('tasks', ['status' => 1 - $task->status]);
    }

    /** @test */
    public function test_lables_required_to_task()
    {
        $task = factory(Task::class)->create(['user_id' => $this->user->id]);

        $response = $this->patchJson('/tasks/' . $task->id . '/labels');

        $response->assertStatus(422);
    }


    /** @test */
    public function test_can_add_labels_to_task()
    {
        $task = factory(Task::class)->create(['user_id' => $this->user->id]);

        $labels = factory(Label::class, 5)->create();

        $response = $this->patchJson('/tasks/' . $task->id . '/labels', ['labels' => $labels->pluck('id')->toArray()]);

        $response->assertStatus(200);

        $this->assertDatabaseCount('label_task', 5);
    }

    /** @test */
    public function test_can_get_tasks()
    {
        $task = factory(Task::class)->create(['user_id' => $this->user->id]);

        $labels = factory(Label::class, 5)->create();

        $task->labels()->sync($labels->pluck('id')->toArray());

        $response = $this->getJson('/tasks');

        $response->assertStatus(200);

        $response->assertJsonCount(1);

        $this->assertObjectHasAttribute('labels', $response->getData()->data[0]);
    }

    /** @test */
    public function test_can_get_task_detail()
    {
        $task = factory(Task::class)->create(['user_id' => $this->user->id]);
        $resource = new TaskResource($task);
        $request  = Request::create('/tasks/' . $task->id, 'GET');

        $this->getJson('/tasks/' . $task->id)
            ->assertStatus(200)
            ->assertExactJson($resource->response($request)->getData(true));
    }

    /** @test */
    public function test_can_not_get_other_users_tasks()
    {
        $collection = factory(Task::class, 2)->create(['user_id' => $this->user->id]);
        factory(Task::class, 2)->create(['user_id' => factory(User::class)->create()->id]);
        $request  = Request::create('/tasks', 'GET');
        $resource = new TaskCollection($collection);

        $this->getJson('/tasks')
            ->assertStatus(200)
            ->assertExactJson($resource->response($request)->getData(true));

        $this->assertDatabaseCount('tasks', 4);
    }

    /** @test */
    public function test_can_not_get_other_users_task_detail()
    {
        $task = factory(Task::class)->create(['user_id' => factory(User::class)->create()->id]);

        $response = $this->getJson('/tasks/' . $task->id)
            ->assertStatus(200);

        $this->assertEmpty($response->getData(true));

        $this->assertDatabaseCount('tasks', 1);
    }

    /** @test */
    public function test_can_send_email_when_task_closed()
    {
        Notification::fake();

        $task = factory(Task::class)->create(['user_id' => $this->user->id]);

        $response = $this->patch('/tasks/' . $task->id . '/status');

        $response->assertStatus(200);

        Notification::assertSentTo(
            [$this->user],
            TaskClosed::class
        );
    }
}
