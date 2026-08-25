<?php

namespace App\Contracts;

interface GeneratesImageVariants
{
    /**
     * Generate an optimized thumbnail and large version of the image stored at
     * $sourcePath on the configured original disk, saving both to the configured
     * public disk under $directory.
     *
     * @return array{thumbnail: string, large: string}
     */
    public function generate(string $sourcePath, string $directory): array;
}
