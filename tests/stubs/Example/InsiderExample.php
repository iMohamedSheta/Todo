<?php

namespace Tests\Stubs\Example;

use IMohamedSheta\Todo\Attributes\TODO;

#[TODO('the class todo list works will inside the stub')]
class InsiderExample
{
    #[TODO('the method todo list works will inside the stub')]
    public function exampleInsiderMethod(): string
    {
        return 'example';
    }
}
