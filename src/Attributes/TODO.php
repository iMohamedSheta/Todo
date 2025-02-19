<?php

namespace IMohamedSheta\Todo\Attributes;

#[\Attribute(\Attribute::TARGET_ALL)]
class TODO
{
    public function __construct(public string $message = 'Not finished yet') {}
}
