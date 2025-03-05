<?php

namespace IMohamedSheta\Todo\Attributes;

use IMohamedSheta\Todo\Enums\Priority;

#[\Attribute(\Attribute::TARGET_ALL)]
class TODO
{
    public function __construct(public string $message = 'Not finished yet', public Priority $priority = Priority::MEDIUM) {}
}
