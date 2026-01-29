<?php namespace Admin\Services\Image\Drivers;

class Gd2 extends AbstractDriver
{
    public function resize()
    {
        return $this->process('resize');
    }

    public function crop()
    {
        return $this->process('crop');
    }

    public function rotate()
    {
        $degs = [90, 180, 270, 'vrt', 'hor'];
        if ($this->parent->rotation_angle === '' OR ! in_array($this->parent->rotation_angle, $degs))
        {
            $this->parent->set_error('imglib_rotation_angle_required');
            return false;
        }

        // Reassign width/height for rotation
        if ($this->parent->rotation_angle == 90 OR $this->parent->rotation_angle == 270)
        {
            $this->parent->width  = $this->parent->orig_height;
            $this->parent->height = $this->parent->orig_width;
        }
        else
        {
            $this->parent->width  = $this->parent->orig_width;
            $this->parent->height = $this->parent->orig_height;
        }

        return ($this->parent->rotation_angle === 'hor' OR $this->parent->rotation_angle === 'vrt')
            ? $this->mirror()
            : $this->rotate_gd();
    }

    public function watermark()
    {
        return ($this->parent->wm_type === 'overlay') ? $this->overlay_watermark() : $this->text_watermark();
    }

    protected function process($action = 'resize')
    {
        $v2_override = false;
        if ($this->parent->dynamic_output === false && $this->parent->orig_width === $this->parent->width && $this->parent->orig_height === $this->parent->height)
        {
            if ($this->parent->source_image !== $this->parent->new_image && @copy($this->parent->full_src_path, $this->parent->full_dst_path))
            {
                chmod($this->parent->full_dst_path, $this->parent->file_permissions);
            }
            return true;
        }

        if ($action === 'crop')
        {
            $this->parent->orig_width  = $this->parent->width;
            $this->parent->orig_height = $this->parent->height;
            
            if ($this->parent->gd_version() !== false)
            {
                $gd_version = str_replace('0', '', (string)$this->parent->gd_version());
                $v2_override = ($gd_version == 2);
            }
        }
        else
        {
            $this->parent->x_axis = 0;
            $this->parent->y_axis = 0;
        }

        if ( ! ($src_img = $this->create_gd()))
        {
            return false;
        }

        if ($this->parent->image_library === 'gd2' && function_exists('imagecreatetruecolor'))
        {
            $create = 'imagecreatetruecolor';
            $copy   = 'imagecopyresampled';
        }
        else
        {
            $create = 'imagecreate';
            $copy   = 'imagecopyresized';
        }

        $dst_img = $create($this->parent->width, $this->parent->height);

        if ($this->parent->image_type == 3) // png
        {
            imagealphablending($dst_img, false);
            imagesavealpha($dst_img, true);
        }

        $copy($dst_img, $src_img, 0, 0, (int)$this->parent->x_axis, (int)$this->parent->y_axis, (int)$this->parent->width, (int)$this->parent->height, (int)$this->parent->orig_width, (int)$this->parent->orig_height);

        if ($this->parent->dynamic_output === true)
        {
            $this->display_gd($dst_img);
        }
        elseif ( ! $this->save_gd($dst_img))
        {
            return false;
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);

        if ($this->parent->dynamic_output !== true)
        {
            chmod($this->parent->full_dst_path, $this->parent->file_permissions);
        }

        return true;
    }

    protected function rotate_gd()
    {
        if ( ! ($src_img = $this->create_gd()))
        {
            return false;
        }
        $white = imagecolorallocate($src_img, 255, 255, 255);
        $dst_img = imagerotate($src_img, (float)$this->parent->rotation_angle, $white);
        if ($this->parent->dynamic_output === true)
        {
            $this->display_gd($dst_img);
        }
        elseif ( ! $this->save_gd($dst_img))
        {
            return false;
        }
        imagedestroy($dst_img);
        imagedestroy($src_img);
        chmod($this->parent->full_dst_path, $this->parent->file_permissions);
        return true;
    }

    protected function mirror()
    {
        if ( ! $src_img = $this->create_gd())
        {
            return false;
        }
        $width  = $this->parent->orig_width;
        $height = $this->parent->orig_height;

        if ($this->parent->rotation_angle === 'hor')
        {
            for ($i = 0; $i < $height; $i++)
            {
                $left = 0;
                $right = $width - 1;
                while ($left < $right)
                {
                    $cl = imagecolorat($src_img, $left, $i);
                    $cr = imagecolorat($src_img, $right, $i);
                    imagesetpixel($src_img, $left, $i, $cr);
                    imagesetpixel($src_img, $right, $i, $cl);
                    $left++;
                    $right--;
                }
            }
        }
        else
        {
            for ($i = 0; $i < $width; $i++)
            {
                $top = 0;
                $bottom = $height - 1;
                while ($top < $bottom)
                {
                    $ct = imagecolorat($src_img, $i, $top);
                    $cb = imagecolorat($src_img, $i, $bottom);
                    imagesetpixel($src_img, $i, $top, $cb);
                    imagesetpixel($src_img, $i, $bottom, $ct);
                    $top++;
                    $bottom--;
                }
            }
        }

        if ($this->parent->dynamic_output === true)
        {
            $this->display_gd($src_img);
        }
        elseif ( ! $this->save_gd($src_img))
        {
            return false;
        }
        imagedestroy($src_img);
        chmod($this->parent->full_dst_path, $this->parent->file_permissions);
        return true;
    }

    protected function overlay_watermark()
    {
        if ( ! function_exists('imagecolortransparent'))
        {
            $this->parent->set_error('imglib_gd_required');
            return false;
        }
        $this->parent->get_image_properties();
        $props = $this->parent->get_image_properties($this->parent->wm_overlay_path, true);
        $wm_img_type = $props['image_type'];
        $wm_width    = $props['width'];
        $wm_height   = $props['height'];

        $wm_img  = $this->create_gd($this->parent->wm_overlay_path, $wm_img_type);
        $src_img = $this->create_gd($this->parent->full_src_path);

        $this->parent->wm_vrt_alignment = strtoupper((string)$this->parent->wm_vrt_alignment[0]);
        $this->parent->wm_hor_alignment = strtoupper((string)$this->parent->wm_hor_alignment[0]);

        if ($this->parent->wm_vrt_alignment === 'B')
            $this->parent->wm_vrt_offset = $this->parent->wm_vrt_offset * -1;
        if ($this->parent->wm_hor_alignment === 'R')
            $this->parent->wm_hor_offset = $this->parent->wm_hor_offset * -1;

        $x_axis = $this->parent->wm_hor_offset + $this->parent->wm_padding;
        $y_axis = $this->parent->wm_vrt_offset + $this->parent->wm_padding;

        if ($this->parent->wm_vrt_alignment === 'M')
        {
            $y_axis += ($this->parent->orig_height / 2) - ($wm_height / 2);
        }
        elseif ($this->parent->wm_vrt_alignment === 'B')
        {
            $y_axis += $this->parent->orig_height - $wm_height;
        }

        if ($this->parent->wm_hor_alignment === 'C')
        {
            $x_axis += ($this->parent->orig_width / 2) - ($wm_width / 2);
        }
        elseif ($this->parent->wm_hor_alignment === 'R')
        {
            $x_axis += $this->parent->orig_width - $wm_width;
        }

        if ($wm_img_type == 3 && function_exists('imagealphablending'))
        {
            @imagealphablending($src_img, true);
        }

        $rgba = imagecolorat($wm_img, (int)$this->parent->wm_x_transp, (int)$this->parent->wm_y_transp);
        $alpha = ($rgba & 0x7F000000) >> 24;

        if ($alpha > 0)
        {
            imagecopy($src_img, $wm_img, (int)$x_axis, (int)$y_axis, 0, 0, (int)$wm_width, (int)$wm_height);
        }
        else
        {
            imagecolortransparent($wm_img, imagecolorat($wm_img, (int)$this->parent->wm_x_transp, (int)$this->parent->wm_y_transp));
            imagecopymerge($src_img, $wm_img, (int)$x_axis, (int)$y_axis, 0, 0, (int)$wm_width, (int)$wm_height, (int)$this->parent->wm_opacity);
        }

        if ($this->parent->image_type == 3)
        {
            imagealphablending($src_img, false);
            imagesavealpha($src_img, true);
        }

        if ($this->parent->dynamic_output === true)
        {
            $this->display_gd($src_img);
        }
        elseif ( ! $this->save_gd($src_img))
        {
            return false;
        }

        imagedestroy($src_img);
        imagedestroy($wm_img);
        return true;
    }

    protected function text_watermark()
    {
        if ( ! ($src_img = $this->create_gd()))
        {
            return false;
        }

        if ($this->parent->wm_use_truetype === true && ! file_exists($this->parent->wm_font_path))
        {
            $this->parent->set_error('imglib_missing_font');
            return false;
        }

        $this->parent->get_image_properties();

        if ($this->parent->wm_vrt_alignment === 'B')
        {
            $this->parent->wm_vrt_offset = $this->parent->wm_vrt_offset * -1;
        }
        if ($this->parent->wm_hor_alignment === 'R')
        {
            $this->parent->wm_hor_offset = $this->parent->wm_hor_offset * -1;
        }

        if ($this->parent->wm_use_truetype === true)
        {
            if (empty($this->parent->wm_font_size))
            {
                $this->parent->wm_font_size = 17;
            }

            if (function_exists('imagettfbbox'))
            {
                $temp = imagettfbbox((float)$this->parent->wm_font_size, 0, (string)$this->parent->wm_font_path, (string)$this->parent->wm_text);
                $temp = $temp[2] - $temp[0];
                $fontwidth = $temp / strlen((string)$this->parent->wm_text);
            }
            else
            {
                $fontwidth = $this->parent->wm_font_size - ($this->parent->wm_font_size / 4);
            }
            $fontheight = $this->parent->wm_font_size;
            $this->parent->wm_vrt_offset += $this->parent->wm_font_size;
        }
        else
        {
            $fontwidth  = imagefontwidth((int)$this->parent->wm_font_size);
            $fontheight = imagefontheight((int)$this->parent->wm_font_size);
        }

        $x_axis = $this->parent->wm_hor_offset + $this->parent->wm_padding;
        $y_axis = $this->parent->wm_vrt_offset + $this->parent->wm_padding;

        if ($this->parent->wm_use_drop_shadow === false)
        {
            $this->parent->wm_shadow_distance = 0;
        }

        $this->parent->wm_vrt_alignment = strtoupper((string)$this->parent->wm_vrt_alignment[0]);
        $this->parent->wm_hor_alignment = strtoupper((string)$this->parent->wm_hor_alignment[0]);

        if ($this->parent->wm_vrt_alignment === 'M')
        {
            $y_axis += ($this->parent->orig_height / 2) + ($fontheight / 2);
        }
        elseif ($this->parent->wm_vrt_alignment === 'B')
        {
            $y_axis += $this->parent->orig_height - $fontheight - $this->parent->wm_shadow_distance - ($fontheight / 2);
        }

        if ($this->parent->wm_hor_alignment === 'R')
        {
            $x_axis += $this->parent->orig_width - ($fontwidth * strlen((string)$this->parent->wm_text)) - $this->parent->wm_shadow_distance;
        }
        elseif ($this->parent->wm_hor_alignment === 'C')
        {
            $x_axis += floor(($this->parent->orig_width - ($fontwidth * strlen((string)$this->parent->wm_text))) / 2);
        }

        if ($this->parent->wm_use_drop_shadow)
        {
            $x_shad = $x_axis + $this->parent->wm_shadow_distance;
            $y_shad = $y_axis + $this->parent->wm_shadow_distance;

            $drp_color = str_split(substr((string)$this->parent->wm_shadow_color, 1, 6), 2);
            $drp_color = imagecolorclosest($src_img, hexdec($drp_color[0]), hexdec($drp_color[1]), hexdec($drp_color[2]));

            if ($this->parent->wm_use_truetype)
            {
                imagettftext($src_img, (float)$this->parent->wm_font_size, 0, (int)$x_shad, (int)$y_shad, $drp_color, (string)$this->parent->wm_font_path, (string)$this->parent->wm_text);
            }
            else
            {
                imagestring($src_img, (int)$this->parent->wm_font_size, (int)$x_shad, (int)$y_shad, (string)$this->parent->wm_text, $drp_color);
            }
        }

        $txt_color = str_split(substr((string)$this->parent->wm_font_color, 1, 6), 2);
        $txt_color = imagecolorclosest($src_img, hexdec($txt_color[0]), hexdec($txt_color[1]), hexdec($txt_color[2]));

        if ($this->parent->wm_use_truetype)
        {
            imagettftext($src_img, (float)$this->parent->wm_font_size, 0, (int)$x_axis, (int)$y_axis, $txt_color, (string)$this->parent->wm_font_path, (string)$this->parent->wm_text);
        }
        else
        {
            imagestring($src_img, (int)$this->parent->wm_font_size, (int)$x_axis, (int)$y_axis, (string)$this->parent->wm_text, $txt_color);
        }

        if ($this->parent->image_type == 3)
        {
            imagealphablending($src_img, false);
            imagesavealpha($src_img, true);
        }

        if ($this->parent->dynamic_output === true)
        {
            $this->display_gd($src_img);
        }
        else
        {
            $this->save_gd($src_img);
        }

        imagedestroy($src_img);
        return true;
    }

    protected function create_gd($path = '', $image_type = '')
    {
        if ($path === '')
        {
            $path = $this->parent->full_src_path;
        }

        if ($image_type === '')
        {
            $image_type = $this->parent->image_type;
        }

        switch ($image_type)
        {
            case 1:
                if ( ! function_exists('imagecreatefromgif'))
                {
                    $this->parent->set_error(['imglib_unsupported_imagecreate', 'imglib_gif_not_supported']);
                    return false;
                }
                return imagecreatefromgif($path);
            case 2:
                if ( ! function_exists('imagecreatefromjpeg'))
                {
                    $this->parent->set_error(['imglib_unsupported_imagecreate', 'imglib_jpg_not_supported']);
                    return false;
                }
                return imagecreatefromjpeg($path);
            case 3:
                if ( ! function_exists('imagecreatefrompng'))
                {
                    $this->parent->set_error(['imglib_unsupported_imagecreate', 'imglib_png_not_supported']);
                    return false;
                }
                return imagecreatefrompng($path);
            default:
                $this->parent->set_error(['imglib_unsupported_imagecreate']);
                return false;
        }
    }

    protected function save_gd($resource)
    {
        switch ($this->parent->image_type)
        {
            case 1:
                if ( ! function_exists('imagegif'))
                {
                    $this->parent->set_error(['imglib_unsupported_imagecreate', 'imglib_gif_not_supported']);
                    return false;
                }
                if ( ! @imagegif($resource, $this->parent->full_dst_path))
                {
                    $this->parent->set_error('imglib_save_failed');
                    return false;
                }
            break;
            case 2:
                if ( ! function_exists('imagejpeg'))
                {
                    $this->parent->set_error(['imglib_unsupported_imagecreate', 'imglib_jpg_not_supported']);
                    return false;
                }
                if ( ! @imagejpeg($resource, $this->parent->full_dst_path, (int)$this->parent->quality))
                {
                    $this->parent->set_error('imglib_save_failed');
                    return false;
                }
            break;
            case 3:
                if ( ! function_exists('imagepng'))
                {
                    $this->parent->set_error(['imglib_unsupported_imagecreate', 'imglib_png_not_supported']);
                    return false;
                }
                if ( ! @imagepng($resource, $this->parent->full_dst_path))
                {
                    $this->parent->set_error('imglib_save_failed');
                    return false;
                }
            break;
            default:
                $this->parent->set_error(['imglib_unsupported_imagecreate']);
                return false;
            break;
        }
        return true;
    }

    protected function display_gd($resource)
    {
        header('Content-Disposition: filename='.$this->parent->source_image.';');
        header('Content-Type: '.$this->parent->mime_type);
        header('Content-Transfer-Encoding: binary');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');

        switch ($this->parent->image_type)
        {
            case 1: imagegif($resource); break;
            case 2: imagejpeg($resource, null, (int)$this->parent->quality); break;
            case 3: imagepng($resource); break;
            default: echo 'Unable to display the image'; break;
        }
    }
}
