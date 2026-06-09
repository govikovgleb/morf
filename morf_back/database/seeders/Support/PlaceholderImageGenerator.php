<?php

declare(strict_types=1);

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

class PlaceholderImageGenerator
{
    private const PALETTES = [
        ['bg' => [100, 149, 237], 'text' => [255, 255, 255]], // cornflowerblue
        ['bg' => [255, 99, 71], 'text' => [255, 255, 255]],   // tomato
        ['bg' => [60, 179, 113], 'text' => [255, 255, 255]],  // mediumseagreen
        ['bg' => [147, 112, 219], 'text' => [255, 255, 255]], // mediumpurple
        ['bg' => [255, 165, 0], 'text' => [0, 0, 0]],         // orange
        ['bg' => [70, 130, 180], 'text' => [255, 255, 255]],  // steelblue
    ];

    public function generateReferenceImage(string $id, int $width = 800, int $height = 600): string
    {
        $filename = "reference_images/{$id}.png";
        $this->createImage($filename, $width, $height, "REF\n{$id}");

        return $this->publicUrl($filename);
    }

    public function generateArtworkImage(string $id, int $width = 800, int $height = 800): string
    {
        $filename = "artworks/{$id}.png";
        $this->createImage($filename, $width, $height, "ART\n{$id}");

        return $this->publicUrl($filename);
    }

    private function createImage(string $relativePath, int $width, int $height, string $label): void
    {
        $palette = self::PALETTES[array_rand(self::PALETTES)];

        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, $palette['bg'][0], $palette['bg'][1], $palette['bg'][2]);
        $textColor = imagecolorallocate($image, $palette['text'][0], $palette['text'][1], $palette['text'][2]);

        imagefill($image, 0, 0, $bgColor);

        // Draw border
        $borderColor = imagecolorallocate($image, 255, 255, 255);
        imagerectangle($image, 10, 10, $width - 10, $height - 10, $borderColor);

        // Add text
        $font = $this->fontPath();
        if ($font !== null) {
            $fontSize = min($width, $height) / 15;
            $bbox = @imagettfbbox($fontSize, 0, $font, $label);
            if ($bbox !== false) {
                $textWidth = $bbox[2] - $bbox[0];
                $textHeight = $bbox[1] - $bbox[7];
                $x = ($width - $textWidth) / 2;
                $y = ($height + $textHeight) / 2;
                imagettftext($image, $fontSize, 0, (int) $x, (int) $y, $textColor, $font, $label);
            } else {
                imagestring($image, 5, $width / 2 - 40, $height / 2 - 10, str_replace("\n", ' ', $label), $textColor);
            }
        } else {
            imagestring($image, 5, $width / 2 - 40, $height / 2 - 10, str_replace("\n", ' ', $label), $textColor);
        }

        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($relativePath, $data);
    }

    private function publicUrl(string $relativePath): string
    {
        return '/storage/'.$relativePath;
    }

    private function fontPath(): ?string
    {
        // Try to find a system TTF font; fallback to built-in if missing
        $candidates = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/System/Library/Fonts/Helvetica.ttc',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
