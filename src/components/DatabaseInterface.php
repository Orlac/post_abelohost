<?php

declare(strict_types=1);
namespace app\components;

interface DatabaseInterface
{
    public function check(): string;
}
