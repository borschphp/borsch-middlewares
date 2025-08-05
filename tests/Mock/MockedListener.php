<?php

namespace AppTest\Mock;

use Borsch\Http\ServerRequest;
use Throwable;

class MockedListener
{
    public function __invoke(Throwable $throwable, ServerRequest $request) {}
}
