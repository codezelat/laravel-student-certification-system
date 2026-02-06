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
            
            // Resize to match orientation dimensions
            $image = imagecreatetruecolor($width, $height);
            imagecopyresampled($image, $background, 0, 0, 0, 0, $width, $height, imagesx($background), imagesy($background));
            imagedestroy($background);
        } else {
            $image = $this->createDefaultBackground($width, $height);
        }

        // Add the participant's name
        $this->addNameToImage($image, $submission->full_name, $width, $height);

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
    private function addNameToImage($image, string $name, int $width, int $height): void
    {
        $fontPath = $this->getFontPath();
        $textColor = imagecolorallocate($image, 0, 51, 102);
        
        // Calculate font size based on name length and image dimensions
        $baseFontSize = ($width > $height) ? 42 : 32;
        $fontSize = min($baseFontSize, $baseFontSize * (20 / max(strlen($name), 10)));
        $fontSize = max($fontSize, 20);

        if ($fontPath) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $name);
            $nameWidth = $bbox[2] - $bbox[0];
            
            // If name is too wide, reduce font size
            while ($nameWidth > $width - 100 && $fontSize > 16) {
                $fontSize -= 2;
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $name);
                $nameWidth = $bbox[2] - $bbox[0];
            }
            
            $nameX = ($width - $nameWidth) / 2;
            $nameY = $height / 2 + 20;
            
            imagettftext($image, $fontSize, 0, (int)$nameX, (int)$nameY, $textColor, $fontPath, $name);
        } else {
            // Fallback to built-in font
            $textWidth = imagefontwidth(5) * strlen($name);
            $nameX = ($width - $textWidth) / 2;
            $nameY = $height / 2;
            imagestring($image, 5, (int)$nameX, (int)$nameY, $name, $textColor);
        }
    }

    /**
     * Get a TrueType font path.
     */
    private function getFontPath(): ?string
    {
        // Try to find a system font
        $possiblePaths = [
            '/System/Library/Fonts/Supplemental/Georgia Bold.ttf',
            '/System/Library/Fonts/Supplemental/Times New Roman Bold.ttf',
            '/System/Library/Fonts/Supplemental/Arial Bold.ttf',
            '/Library/Fonts/Georgia Bold.ttf',
            '/Library/Fonts/Times New Roman Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf',
            storage_path('app/fonts/certificate-font.ttf'),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
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
