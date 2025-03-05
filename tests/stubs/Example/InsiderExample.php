<?php

namespace Tests\Stubs\Example;

use IMohamedSheta\Todo\Attributes\TODO;
use IMohamedSheta\Todo\Enums\Priority;

#[TODO('the class todo list works will inside the stub', Priority::CRITICAL)]
class InsiderExample
{
    #[TODO('the method todo list works will inside the stub')]
    public function exampleInsiderMethod(): string
    {
        return 'example';
    }
}
