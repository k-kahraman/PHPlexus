<?php

namespace PHPlexus\Repository;

use PHPlexus\Model\Model;

class Repository {
    private Model $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function fetchData(): string {
        return $this->model->getData();
    }
}