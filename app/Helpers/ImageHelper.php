<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Storage::url() on a null/empty path returns an empty-src <img>, which
     * renders as a broken-image icon rather than failing loudly — easy to
     * miss until a product (or company logo, sponsor logo, etc.) that
     * hasn't had an image uploaded yet shows up on a live page.
     *
     * Drop-in replacement for Storage::url($path) with a placeholder
     * fallback when $path is null/empty.
     */
    public static function url(?string $path): string
    {
        return $path
            ? Storage::url($path)
            : asset('images/no-image.svg');
    }

    /**
     * Downscale-only resize + re-encode for admin-uploaded images, meant to
     * be wired into a Filament FileUpload's ->saveUploadedFileUsing(). A
     * phone/camera photo dropped into the admin panel can easily be 3000px+
     * wide and several MB — this caps it to something sane and re-encodes
     * as WebP (falling back to JPEG if the server's GD build lacks WebP
     * support, which the resize/encode round trip is verified against —
     * including that alpha transparency survives the conversion).
     *
     * Never upscales: an image already smaller than the target box is only
     * re-encoded, not stretched up.
     *
     * Returns the stored path (relative to $disk), matching what
     * saveUploadedFileUsing() is expected to return.
     */
    public static function optimizeAndStore(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1920,
        int $maxHeight = 1920,
        int $quality = 82,
        string $disk = 'public'
    ): string {
        // If the gd extension isn't installed/enabled at all, every GD
        // function below (imagecreatefromstring, imagewebp, ...) is
        // *undefined*, not just failing — PHP throws a fatal "Call to
        // undefined function" Error, which the @ operator further down
        // cannot suppress (@ only silences warnings/notices, never a
        // missing-function fatal). Bail out to the untouched-original path
        // immediately, same as the "GD couldn't decode it" fallback below,
        // instead of taking the whole request down with it.
        if (! extension_loaded('gd') || ! function_exists('imagecreatefromstring')) {
            return $file->store($directory, $disk);
        }

        $data = @file_get_contents($file->getRealPath());
        $source = $data !== false ? @imagecreatefromstring($data) : false;

        // GD couldn't decode it (e.g. an SVG logo) — store the original
        // untouched rather than losing the upload.
        if (! $source) {
            return $file->store($directory, $disk);
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1.0);

        if ($ratio < 1.0) {
            $newWidth = max(1, (int) round($width * $ratio));
            $newHeight = max(1, (int) round($height * $ratio));
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        $useWebp = function_exists('imagewebp');
        $extension = $useWebp ? 'webp' : 'jpg';

        ob_start();
        if ($useWebp) {
            imagewebp($source, null, $quality);
        } else {
            imagejpeg($source, null, $quality);
        }
        $encoded = ob_get_clean();
        imagedestroy($source);

        $path = trim($directory, '/').'/'.Str::ulid().'.'.$extension;
        Storage::disk($disk)->put($path, $encoded);

        return $path;
    }
}
