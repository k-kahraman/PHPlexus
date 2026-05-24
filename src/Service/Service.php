<?php

namespace PHPlexus\Service;

use PHPlexus\Repository\Repository;

class Service {
    private Repository $repository;

    public function __construct(Repository $repository) {
        $this->repository = $repository;
    }

    public function serveData(): string {
        return $this->repository->fetchData();
    }
}