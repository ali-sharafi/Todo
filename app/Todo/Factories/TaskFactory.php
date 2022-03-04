<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Todo\Contract\TaskInterface;
use Todo\Model\Task;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'description' => $faker->text,
        'status' => TaskInterface::TASK_OPEN,
    ];
});
