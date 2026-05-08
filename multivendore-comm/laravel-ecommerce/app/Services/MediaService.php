<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Upload and optionally resize/optimize an image.
     * Returns the public S3 URL.
     */
    public function uploadImage(
        UploadedFile $file,
        string       $directory,
        int          $maxWidth  = 1920,
        int          $maxHeight = 1920,
        int          $quality   = 85,
    ): string {
        $ext      = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::uuid() . '.' . $ext;
        $path     = "{$directory}/{$filename}";

        $image = $this->manager->read($file->getRealPath());

        // Only scale down — never upscale
        if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
            $image->scaleDown($maxWidth, $maxHeight);
        }

        $encoded = $image->toJpeg($quality);

        Storage::disk('s3')->put($path, (string) $encoded, 'public');

        return Storage::disk('s3')->url($path);
    }

    /** Upload a raw file (video, PDF, etc.) without processing. */
    public function uploadFile(UploadedFile $file, string $directory): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = "{$directory}/{$filename}";

        Storage::disk('s3')->putFileAs($directory, $file, $filename, 'public');

        return Storage::disk('s3')->url($path);
    }

    /** Delete a file from S3 by its URL. */
    public function delete(string $url): bool
    {
        $path = $this->urlToPath($url);
        return Storage::disk('s3')->delete($path);
    }

    /** Generate responsive image variants (thumbnail, medium, large). */
    public function generateVariants(UploadedFile $file, string $directory): array
    {
        $sizes = ['thumb' => [300, 300], 'medium' => [800, 800], 'large' => [1920, 1920]];
        $urls  = [];

        foreach ($sizes as $name => [$w, $h]) {
            $urls[$name] = $this->uploadImage($file, "{$directory}/{$name}", $w, $h);
        }

        return $urls;
    }

    private function urlToPath(string $url): string
    {
        $base = config('filesystems.disks.s3.url', '');
        return ltrim(str_replace($base, '', $url), '/');
    }
}
