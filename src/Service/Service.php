<?php

namespace PHPlexus\Service;

use PHPlexus\Core\Entity;
use PHPlexus\Repository\Repository;

class Service {
    private $repository;

    public function __construct(Repository $repository) {
        $this->repository = $repository;
    }

    public function serveData() {
        return $this->repository->fetchData();
    }
}