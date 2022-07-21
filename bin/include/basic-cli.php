<?php
declare(strict_types=1);

define('GREY', "\033[1;90m");
define('YELLOW', "\033[0;33m");
define('RED', "\033[0;31m");
define('GREEN', "\033[1;32m");
define('RESET', "\033[0m");

function error(string $msg): string
{
    return RED . $msg . RESET;
}

function warning(string $msg): string
{
    return YELLOW . $msg . RESET;
}

function happy(string $msg): string
{
    return GREEN . $msg . RESET;
}

function text(string $msg): string
{
    return GREY . $msg . RESET;
}

function printLine(string $msg): void
{
    echo $msg . "\n";
}
