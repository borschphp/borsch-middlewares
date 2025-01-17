<?php

namespace AppTest\Mock;

use Laminas\Diactoros\ServerRequest;
use Throwable;

class MockedListener
{
    public function __invoke(Throwable $throwable, ServerRequest $request) {}
}
