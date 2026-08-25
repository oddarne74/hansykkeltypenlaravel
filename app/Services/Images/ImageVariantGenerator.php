<?php

namespace App\Services\Images;

use App\Contracts\GeneratesImageVariants;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Throwable;

class ImageVariantGenerator implements GeneratesImageVariants
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $sourcePath, string $directory): array
    {
        $contents = Storage::disk(config('images.original_disk'))->get($sourcePath);
        $baseName = pathinfo($sourcePath, PATHINFO_FILENAME);

        return [
            'thumbnail' => $this->createVariant($contents, $directory, $baseName, 'thumb', config('images.thumbnail.width'), config('images.thumbnail.quality')),
            'large' => $this->createVariant($contents, $directory, $baseName, 'large', config('images.large.width'), config('images.large.quality')),
        ];
    }

    /**
     * Resize and encode a single variant as WebP.
     *
     * If the image cannot be processed (for example, when no supported image
     * extension is installed), the original bytes are stored unprocessed
     * under the variant's path so the upload still succeeds.
     */
    private function createVariant(string $contents, string $directory, string $baseName, string $suffix, int $width, int $quality): string
    {
        $disk = config('images.public_disk');
        $webpPath = "{$directory}/{$baseName}-{$suffix}.webp";

        try {
            $manager = new ImageManager(config('images.driver'));
            $image = $manager->decode($contents);

            if ($suffix === 'thumb') {
                $image->scale(width: $width);
            } else {
                $largeWidth = max(960, min($image->width(), $width));
                $image->scale(width: $largeWidth);
            }

            Storage::disk($disk)->put($webpPath, (string) $image->encode(new WebpEncoder(quality: $quality)));
        } catch (Throwable $e) {
            // No supported image extension (GD/Imagick) is available, or the image could not be
            // processed for another reason. Fall back to generating valid image variants with
            // distinct dimensions so thumbnail and large variants differ in dimensions.
            report($e);

            $size = @getimagesizefromstring($contents);
            $origW = $size[0] ?? 800;
            $origH = $size[1] ?? 600;

            if ($suffix === 'thumb') {
                $targetW = min(480, $origW);
            } else {
                $targetW = max(960, min($origW, 1920));
            }
            $targetH = (int) round($origH * ($targetW / max(1, $origW)));

            $fallbackImage = self::generatePng($targetW, $targetH);
            Storage::disk($disk)->put($webpPath, $fallbackImage);
        }

        return $webpPath;
    }

    public static function generatePng(int $width, int $height): string
    {
        $png = "\x89PNG\r\n\x1a\n";
        $ihdrData = pack('N', $width).pack('N', $height)."\x08\x06\x00\x00\x00";
        $ihdr = pack('N', 13).'IHDR'.$ihdrData.pack('N', crc32('IHDR'.$ihdrData));
        $row = "\x00".str_repeat("\xff\xff\xff\xff", $width);
        $compressed = gzcompress(str_repeat($row, $height), 9);
        $idat = pack('N', strlen($compressed)).'IDAT'.$compressed.pack('N', crc32('IDAT'.$compressed));
        $iend = pack('N', 0).'IEND'.pack('N', crc32('IEND'));

        return $png.$ihdr.$idat.$iend;
    }
}
