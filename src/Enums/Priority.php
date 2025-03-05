<?php

namespace IMohamedSheta\Todo\Enums;

enum Priority: int
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;
    case CRITICAL = 4;

    private const RESET = "\e[0m";

    public function color(): string
    {
        return match ($this) {
            Priority::LOW => "\e[32m",       // Green
            Priority::MEDIUM => "\e[33m",    // Yellow
            Priority::HIGH => "\e[31m",      // Red
            Priority::CRITICAL => "\e[41;97m", // Bright Red
        };
    }

    public function label(): string
    {
        return $this->color().
            match ($this) {
                self::LOW => 'Low',
                self::MEDIUM => 'Medium',
                self::HIGH => 'High',
                self::CRITICAL => 'Critical',
            }
        .self::RESET;
    }
}
