<?php

declare(strict_types=1);

namespace App\Service;

use Exception;

interface QtiItemPackageCreatorInterface
{
    /**
     * @throws Exception
     */
    public function createQtiItemPackage(string $id, array $quiz, string $label): string;
}
