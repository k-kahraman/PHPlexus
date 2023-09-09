<?php
namespace PHPlexus\Repository;

use PHPlexus\Model\Model;


class Repository {
    private $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function fetchData() {
        return $this->model->getData();
    }
}