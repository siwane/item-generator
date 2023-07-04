<?php

declare(strict_types=1);

namespace App\Service;

interface TaoPublisherInterface
{
    public function publishPackage(string $filePath): string;
}
