<?php

namespace Todo\Http\Controllers;

use Todo\Http\Resources\LabelCollection;
use Todo\Http\Resources\LabelResource;
use Todo\Infrustructure\BaseController;
use Todo\Model\Label;

class LabelController extends BaseController
{
    public function store()
    {
        $this->request->validate([
            'name' => 'required|unique:labels|max:150'
        ]);

        return Label::create(['name' => $this->request->name]);
    }

    public function index()
    {
        return new LabelCollection(Label::all());
    }
}
