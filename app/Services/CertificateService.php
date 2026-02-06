<?php

namespace App\Services;

use App\Models\Form;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * A4 dimensions at 96 DPI
     */
    const HORIZONTAL_WIDTH = 1123;
    const HORIZONTAL_HEIGHT = 794;
    const VERTICAL_WIDTH = 794;
    const VERTICAL_HEIGHT = 1123;

    /**
     * Generate a certificate image with the participant's name.
     */
    public function generate(Form $form, Submission $submission): string
    {
        // Determine dimensions based on orientation
        if ($form->orientation === 'horizontal') {
            $width = self::HORIZONTAL_WIDTH;
            $height = self::HORIZONTAL_HEIGHT;
        } else {
            $width = self::VERTICAL_WIDTH;
            $height = self::VERTICAL_HEIGHT;
        }

        // Load the background image or create a default one
        if ($form->certificate_image && Storage::disk('public')->exists($form->certificate_image)) {
            $backgroundPath = Storage::disk('public')->path($form->certificate_image);
            $extension = strtolower(pathinfo($backgroundPath, PATHINFO_EXTENSION));
            
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $background = imagecreatefromjpeg($backgroundPath);
                    break;
                case 'png':
                    $background = imagecreatefrompng($backgroundPath);
                    break;
                default:
                    $background = $this->createDefaultBackground($width, $height);
            }
            
            // Resize to match orientation dimensions based on fit mode
            $fit = $form->certificate_settings['background_fit'] ?? 'fill';
            $image = imagecreatetruecolor($width, $height);
            
            // Default white background
            $white = imagecolorallocate($image, 255, 255, 255);
            imagefill($image, 0, 0, $white);

            $srcW = imagesx($background);
            $srcH = imagesy($background);
            
            $dstX = 0; $dstY = 0;
            $dstW = $width; $dstH = $height;
            $srcX = 0; $srcY = 0;
            
            if ($fit === 'cover') {
                // Determine crop area of source image to cover the canvas
                $imgRatio = $srcW / $srcH;
                $canvasRatio = $width / $height;
                
                if ($canvasRatio > $imgRatio) {
                   // Canvas is wider than image. Image needs to be cropped top/bottom to match canvas width ratio.
                   $srcNewH = $srcW / $canvasRatio;
                   $srcY = ($srcH - $srcNewH) / 2;
                   $srcH = $srcNewH;
                } else {
                   // Canvas is taller than image. Image needs to be cropped left/right.
                   $srcNewW = $srcH * $canvasRatio;
                   $srcX = ($srcW - $srcNewW) / 2;
                   $srcW = $srcNewW;
                }
            } elseif ($fit === 'contain') {
                // Determine destination rect to fit image inside canvas
                $imgRatio = $srcW / $srcH;
                $canvasRatio = $width / $height;
                
                if ($canvasRatio > $imgRatio) {
                    // Canvas is wider. Fit by height, letterbox sides.
                    $dstW = $height * $imgRatio;
                    $dstX = ($width - $dstW) / 2;
                } else {
                    // Canvas is taller. Fit by width, letterbox top/bottom.
                    $dstH = $width / $imgRatio;
                    $dstY = ($height - $dstH) / 2;
                }
            }

            imagecopyresampled($image, $background, (int)$dstX, (int)$dstY, (int)$srcX, (int)$srcY, (int)$dstW, (int)$dstH, (int)$srcW, (int)$srcH);
            imagedestroy($background);
        } else {
            $image = $this->createDefaultBackground($width, $height);
        }

        // Add the participant's name
        $this->addNameToImage($image, $submission->full_name, $width, $height, $form);

        // Save to temp file
        $tempPath = storage_path('app/temp/certificate_' . $submission->id . '_' . time() . '.png');
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        imagepng($image, $tempPath);
        imagedestroy($image);

        return $tempPath;
    }

    /**
     * Create a default certificate background.
     */
    private function createDefaultBackground(int $width, int $height)
    {
        $image = imagecreatetruecolor($width, $height);
        
        // White background
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);
        
        return $image;
    }

    /**
     * Add the participant's name to the certificate.
     */
    private function addNameToImage($image, string $name, int $width, int $height, Form $form): void
    {
        // Font Path will be determined later based on settings
        // $fontPath = $this->getFontPath(); // Removed

        $settings = $form->certificate_settings ?? [];

        // Font Color
        $hexColor = $settings['font_color'] ?? '#003366';
        if (preg_match('/^#?([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i', $hexColor, $matches)) {
            $textColor = imagecolorallocate($image, hexdec($matches[1]), hexdec($matches[2]), hexdec($matches[3]));
        } else {
            $textColor = imagecolorallocate($image, 0, 51, 102);
        }

        // Font Size
        $baseFontSize = ($width > $height) ? 42 : 32;
        $fontSize = isset($settings['font_size']) ? (int)$settings['font_size'] : $baseFontSize;
        
        // Dynamic Scaling if no custom settings used (legacy behavior), but if user set size, respect it.
        if (!isset($settings['font_size'])) {
             $fontSize = min($baseFontSize, $baseFontSize * (20 / max(strlen($name), 10)));
             $fontSize = max($fontSize, 20);
        }

        // Coordinates
        $x = $settings['x'] ?? null;
        $y = $settings['y'] ?? null;

        // Font Settings
        $fontWeight = $settings['font_weight'] ?? 'bold';
        $fontStyle = $settings['font_style'] ?? 'normal';
        
        $fontPath = $this->getFontPath($fontWeight, $fontStyle);

        if ($fontPath) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $name);
            $textWidth = $bbox[2] - $bbox[0];
            $textHeight = $bbox[1] - $bbox[7];

            // If X is not set, center horizontally
            if ($x === null) {
                 $x = ($width - $textWidth) / 2;
            }
            
            // If Y is not set, center vertically
            if ($y === null) {
                 $y = ($height / 2) + ($textHeight / 2);
            }

            imagettftext($image, $fontSize, 0, (int)$x, (int)$y, $textColor, $fontPath, $name);
        } else {
            // Fallback to built-in font
            $textWidth = imagefontwidth(5) * strlen($name);
            
            if ($x === null) $x = ($width - $textWidth) / 2;
            if ($y === null) $y = $height / 2;
            
            imagestring($image, 5, (int)$x, (int)$y, $name, $textColor);
        }
    }

    /**
     * Get a TrueType font path based on weight and style.
     */
    private function getFontPath($weight = 'bold', $style = 'normal'): ?string
    {
        $families = ['Georgia', 'Times New Roman', 'Arial', 'DejaVuSerif', 'LiberationSerif'];
        $basePaths = [
            '/System/Library/Fonts/Supplemental/',
            '/Library/Fonts/',
            '/usr/share/fonts/truetype/dejavu/',
            '/usr/share/fonts/truetype/liberation/',
            storage_path('app/fonts/'),
        ];

        // Construct suffix based on style
        // E.g. " Bold", " Italic", " Bold Italic"
        $suffixes = [];
        
        if ($weight === 'bold' && $style === 'italic') {
            $suffixes[] = ' Bold Italic';
            $suffixes[] = 'BoldItalic';
            $suffixes[] = '-BoldItalic';
        } elseif ($weight === 'bold') {
            $suffixes[] = ' Bold';
            $suffixes[] = 'Bold';
            $suffixes[] = '-Bold';
        } elseif ($style === 'italic') {
            $suffixes[] = ' Italic';
            $suffixes[] = 'Italic';
            $suffixes[] = '-Italic';
        } else {
            $suffixes[] = '';
            $suffixes[] = ' Regular';
            $suffixes[] = '-Regular';
        }

        foreach ($families as $family) {
            foreach ($suffixes as $suffix) {
                $filename = $family . $suffix . '.ttf';
                
                foreach ($basePaths as $basePath) {
                    $fullPath = $basePath . $filename;
                    if (file_exists($fullPath)) {
                        return $fullPath;
                    }
                }
            }
        }

        // Final fallback to the specific file if it exists (legacy support)
        if (file_exists(storage_path('app/fonts/certificate-font.ttf'))) {
            return storage_path('app/fonts/certificate-font.ttf');
        }

        return null;
    }

    /**
     * Generate certificate preview (for displaying in browser).
     */
    public function generatePreview(Form $form, Submission $submission): string
    {
        $tempPath = $this->generate($form, $submission);
        $imageData = file_get_contents($tempPath);
        unlink($tempPath);
        
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}
