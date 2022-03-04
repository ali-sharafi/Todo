<?php

namespace Todo;

use Illuminate\Contracts\Routing\Registrar as Router;

class ApiRouteRegistrar
{

    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    public function all()
    {
        $this->forLabels();
        $this->forTasks();
    }

    public function forTasks()
    {
        $this->router->group(['prefix' => 'tasks'], function ($router) {
            $router->post('', 'TaskController@store')->name('tasks.store');
            $router->get('', 'TaskController@index')->name('tasks.index');
            $router->patch('{task}/labels', 'TaskController@updateLabels')->name('tasks.updateLabels');
            $router->patch('{task}/status', 'TaskController@changeStatus')->name('tasks.updateStatus');
            $router->get('{task}', 'TaskController@getTaskDetail')->name('tasks.detail');
            $router->patch('{task}', 'TaskController@update')->name('tasks.update');
        });

    }

    public function forLabels()
    {
        $this->router->group(['prefix' => 'labels'], function ($router) {
            $router->post('', 'LabelController@store')->name('lables.store');
            $router->get('', 'LabelController@index')->name('lables.index');
        });

    }
}
