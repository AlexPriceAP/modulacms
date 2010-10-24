<?php

/**
 * @ingroup imageadaptor
 */
class GdImageAdaptor extends ImageAdaptor
{
    
    function resize($file, $output, $width = 0, $height = 0, $aspectratio = true)
    {
        if ($height <= 0 && $width <= 0) {
            return false;
        }

        $info = getimagesize($file);

        $new_width = 0;
        $new_height = 0;
        list($old_width, $old_height) = $info;

        /**
         * Check if image is an animated gif and that it's smaller than our
         * specified resolution, do a straight copy with no processing if that
         * if the case.
         */
        if ($this->isAnimatedGif($file)) {
            if ($old_width <= $width && $old_height <= $height) {
                $fs = new FileSystem();
                $fs->copyFile($file, $output, true);
                return true;
            }
        }

        if ($aspectratio) {
            if ($width == 0) $factor = $height / $old_height;
            elseif ($height == 0) $factor = $width / $old_width;
            else $factor = min($width / $old_width, $height / $old_height);
            $new_width = round($old_width * $factor);
            $new_height = round($old_height * $factor);
        } else {
            $new_width = ( $width <= 0 ) ? $old_width : $width;
            $new_height = ( $height <= 0 ) ? $old_height : $height;
        }

        switch ($info[2]) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
                break;
            default:
                return false;
        }

        $image_resized = imagecreatetruecolor($new_width, $new_height);

        if ($info[2] == IMAGETYPE_GIF || $info[2] == IMAGETYPE_PNG) {
            $transparent_index = imagecolortransparent($image);
            // If we have a specific transparent color
            if ($transparent_index >= 0) {
                imagepalettecopy($image, $image_resized);
                imagefill($image_resized, 0, 0, $transparent_index);
                imagecolortransparent($image_resized, $transparent_index);
                imagetruecolortopalette($image_resized, true, 256);
            } elseif ($info[2] == IMAGETYPE_PNG) {
                // Turn off transparency blending (temporarily)
                imagealphablending($image_resized, false);
                // Create a new transparent color for image
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                // Completely fill the background of the new image with allocated color.
                imagefill($image_resized, 0, 0, $color);
                // Restore transparency blending
                imagesavealpha($image_resized, true);
            }
        }

        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

        switch ($info[2]) {
            case IMAGETYPE_GIF:
                imagegif($image_resized, $output);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($image_resized, $output);
                break;
            case IMAGETYPE_PNG:
                imagepng($image_resized, $output);
                break;
            default:
                return false;
        }

        return true;
    }
    
    private function isAnimatedGif($filename)
    {
        return (bool) preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents($filename));
    }

}

?>