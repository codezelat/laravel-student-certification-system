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
        // High Quality Scale Factor
        $scale = 3;

        // Determine dimensions based on orientation
        if ($form->orientation === 'horizontal') {
            $width = self::HORIZONTAL_WIDTH * $scale;
            $height = self::HORIZONTAL_HEIGHT * $scale;
        } else {
            $width = self::VERTICAL_WIDTH * $scale;
            $height = self::VERTICAL_HEIGHT * $scale;
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
        $settings = $form->certificate_settings ?? [];

        // Determine Scale Factor
        $standardWidth = ($form->orientation === 'horizontal' ? self::HORIZONTAL_WIDTH : self::VERTICAL_WIDTH);
        $scale = $width / $standardWidth;

        // Font Settings
        $fontWeight = $settings['font_weight'] ?? 'bold';
        $fontStyle = $settings['font_style'] ?? 'normal';
        $fontPath = $this->getFontPath($fontWeight, $fontStyle);

        // Font Color
        $hexColor = $settings['font_color'] ?? '#003366';
        if (preg_match('/^#?([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i', $hexColor, $matches)) {
            $textColor = imagecolorallocate($image, hexdec($matches[1]), hexdec($matches[2]), hexdec($matches[3]));
        } else {
            $textColor = imagecolorallocate($image, 0, 51, 102);
        }

        // Settings (Scaled)
        $msgFontSize = isset($settings['font_size']) ? (int)$settings['font_size'] : 42;
        $originalFontSize = $msgFontSize * $scale;
        
        $x = isset($settings['x']) ? $settings['x'] * $scale : null;
        $y = isset($settings['y']) ? $settings['y'] * $scale : null;
        $maxWidth = isset($settings['max_width']) ? $settings['max_width'] * $scale : null;
        
        $maxLines = $settings['max_lines'] ?? 1;
        $textAlign = $settings['text_align'] ?? 'center';

        if ($fontPath) {
            try {
                // Positioning Default
                if ($x === null) {
                    // Use a temporary calculation for default centering
                    $bbox = imagettfbbox($originalFontSize, 0, $fontPath, $name);
                    $initialTextWidth = $bbox[2] - $bbox[0];
                    $x = ($width - ($maxWidth ?? $initialTextWidth)) / 2;
                }
                if ($y === null) $y = $height / 2;

                // --- Auto-Scaling & Wrapping Loop ---
                $currentFontSize = $originalFontSize;
                $finalLines = [$name]; // Fallback
                
                // Iterate to find best fit
                while ($currentFontSize >= 10) {
                    // Try to wrap text into lines with current font size
                    $words = explode(' ', $name);
                    $lines = [];
                    $currentLine = '';

                    foreach ($words as $word) {
                        $testLine = $currentLine === '' ? $word : $currentLine . ' ' . $word;
                        
                        // Measure
                        $box = imagettfbbox($currentFontSize, 0, $fontPath, $testLine);
                        $lineWidth = $box[2] - $box[0];
                        
                        // If we have a max width and this exceeds it, wrap
                        if ($maxWidth && $lineWidth > $maxWidth && $currentLine !== '') {
                            $lines[] = $currentLine;
                            $currentLine = $word;
                        } else {
                            $currentLine = $testLine;
                        }
                    }
                    if ($currentLine !== '') $lines[] = $currentLine;
                    
                    // Check constraints
                    $fits = true;

                    // 1. Line Count
                    if (count($lines) > $maxLines) {
                        $fits = false;
                    }
                    
                    // 2. Max Width Check (ensure no single line exceeds width)
                    if ($fits && $maxWidth) {
                        foreach ($lines as $line) {
                             $box = imagettfbbox($currentFontSize, 0, $fontPath, $line);
                             if (($box[2] - $box[0]) > $maxWidth) {
                                 $fits = false; break;
                             }
                        }
                    }
                    
                    if ($fits) {
                        $finalLines = $lines;
                        break;
                    }

                    $currentFontSize -= 2; // Reduce and retry
                }
                
                // --- Drawing ---
                // Use 1.2 line height ratio to match CSS roughly
                $lineHeight = $currentFontSize * 1.2; 
                $totalBlockHeight = count($finalLines) * $lineHeight;
                
                // Vertical Alignment
                $verticalAlign = $settings['vertical_align'] ?? 'top';
                
                // Calculate Top Y of the text block based on anchor $y
                $topY = $y;

                if ($verticalAlign === 'middle') {
                    // $y is the vertical center anchor
                    $topY = $y - ($totalBlockHeight / 2);
                } elseif ($verticalAlign === 'bottom') {
                    // $y is the bottom anchor
                    $topY = $y - $totalBlockHeight;
                }
                
                // Start draw Y calculation (Baseline of first line)
                // Baseline is roughly Top + (FontSize * 0.8) to account for ascent
                $startDrawY = $topY + ($currentFontSize * 0.8);

                foreach ($finalLines as $i => $line) {
                    $box = imagettfbbox($currentFontSize, 0, $fontPath, $line);
                    $w = $box[2] - $box[0];
                    
                    // Calc X Alignment
                    $drawX = $x;
                    if ($maxWidth) {
                        if ($textAlign === 'center') $drawX = $x + ($maxWidth - $w) / 2;
                        elseif ($textAlign === 'right') $drawX = $x + $maxWidth - $w;
                        // Left is default ($x)
                    } else {
                        // Legacy behavior constraint
                        if ($textAlign === 'center') $drawX = $x - ($w / 2);
                    }
                    
                    $drawY = $startDrawY + ($i * $lineHeight);

                    imagettftext($image, $currentFontSize, 0, (int)$drawX, (int)$drawY, $textColor, $fontPath, $line);
                }
            } catch (\Throwable $e) {
                // If TTF fails (e.g. font not found or GD error), log it and fallback
                \Illuminate\Support\Facades\Log::error('Certificate Font Error: ' . $e->getMessage());
                
                // Fallback to built-in font
                $this->useFallbackFont($image, $name, $width, $height, $x, $y, $textColor);
            }
        } else {
            // Fallback to built-in font
            $this->useFallbackFont($image, $name, $width, $height, $x, $y, $textColor);
        }
    }

    /**
     * Get a TrueType font path based on weight and style.
     */
    private function getFontPath($weight = 'bold', $style = 'normal'): ?string
    {
        $fontName = 'Roboto-Regular.ttf';
        if ($weight === 'bold') {
            $fontName = 'Roboto-Bold.ttf';
        }

        $localPath = storage_path('app/fonts/' . $fontName);

        // 1. Check if font exists locally
        if (file_exists($localPath)) {
            return $localPath;
        }

        // 2. Download from Google Fonts (GitHub mirror for direct raw file access)
        // Using Apache OpenSans or Roboto from reliable source
        $url = '';
        if ($weight === 'bold') {
            $url = 'https://github.com/google/fonts/raw/main/apache/roboto/Roboto-Bold.ttf';
        } else {
            $url = 'https://github.com/google/fonts/raw/main/apache/roboto/Roboto-Regular.ttf';
        }

        try {
            // Ensure directory exists
            if (!file_exists(dirname($localPath))) {
                mkdir(dirname($localPath), 0755, true);
            }

            // Download file
            $content = file_get_contents($url);
            if ($content) {
                file_put_contents($localPath, $content);
                return $localPath;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to download Google Font: ' . $e->getMessage());
        }

        // 3. Fallback to existing logic (system paths) if download fails
        $families = ['Roboto', 'OpenSans', 'Lato', 'Arial']; 
        $suffixes = ['-Bold', '-Regular', 'Bold', 'Regular', ''];

        foreach ($families as $family) {
            foreach ($suffixes as $suffix) {
                $filename = $family . $suffix . '.ttf';
                $path = storage_path('app/fonts/' . $filename);
                if (file_exists($path)) {
                    return $path;
                }
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

    /**
     * Fallback to built-in font if TrueType fails.
     */
    private function useFallbackFont($image, $name, $width, $height, $x, $y, $textColor)
    {
        // Built-in font 5 is the largest
        $fw = imagefontwidth(5);
        $fh = imagefontheight(5);
        $len = strlen($name);
        $textWidth = $fw * $len;
        
        if ($x === null) $x = ($width - $textWidth) / 2;
        if ($y === null) {
            // Center vertically if no Y provided
            $y = ($height - $fh) / 2;
        }
        
        imagestring($image, 5, (int)$x, (int)$y, $name, $textColor);
    }
}
