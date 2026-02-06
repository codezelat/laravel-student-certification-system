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
        
        // Cream/off-white background
        $bgColor = imagecolorallocate($image, 255, 250, 240);
        imagefill($image, 0, 0, $bgColor);
        
        // Add a border
        $borderColor = imagecolorallocate($image, 139, 90, 43);
        $borderWidth = 20;
        
        // Outer border
        imagerectangle($image, $borderWidth, $borderWidth, $width - $borderWidth, $height - $borderWidth, $borderColor);
        imagerectangle($image, $borderWidth + 5, $borderWidth + 5, $width - $borderWidth - 5, $height - $borderWidth - 5, $borderColor);
        
        // Add "CERTIFICATE" text at top
        $titleColor = imagecolorallocate($image, 51, 51, 51);
        $title = "CERTIFICATE";
        
        // Use default font
        $fontPath = $this->getFontPath();
        if ($fontPath) {
            $fontSize = ($width > $height) ? 48 : 36;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $title);
            $titleX = ($width - ($bbox[2] - $bbox[0])) / 2;
            $titleY = ($height > $width) ? 150 : 120;
            imagettftext($image, $fontSize, 0, (int)$titleX, (int)$titleY, $titleColor, $fontPath, $title);
            
            // Add "OF PARTICIPATION" subtitle
            $subtitle = "OF PARTICIPATION";
            $subFontSize = ($width > $height) ? 24 : 18;
            $bbox = imagettfbbox($subFontSize, 0, $fontPath, $subtitle);
            $subtitleX = ($width - ($bbox[2] - $bbox[0])) / 2;
            $subtitleY = $titleY + 50;
            imagettftext($image, $subFontSize, 0, (int)$subtitleX, (int)$subtitleY, $titleColor, $fontPath, $subtitle);
            
            // Add "This is to certify that" text
            $certifyText = "This is to certify that";
            $certifyFontSize = ($width > $height) ? 18 : 14;
            $bbox = imagettfbbox($certifyFontSize, 0, $fontPath, $certifyText);
            $certifyX = ($width - ($bbox[2] - $bbox[0])) / 2;
            $certifyY = $height / 2 - 60;
            $grayColor = imagecolorallocate($image, 100, 100, 100);
            imagettftext($image, $certifyFontSize, 0, (int)$certifyX, (int)$certifyY, $grayColor, $fontPath, $certifyText);
        }

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
            
            // Add underline below the name
            $lineColor = imagecolorallocate($image, 139, 90, 43);
            $lineY = $nameY + 20;
            $lineStartX = ($width - $nameWidth) / 2 - 20;
            $lineEndX = ($width + $nameWidth) / 2 + 20;
            imageline($image, (int)$lineStartX, (int)$lineY, (int)$lineEndX, (int)$lineY, $lineColor);
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
