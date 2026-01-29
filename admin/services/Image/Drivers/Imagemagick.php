<?php namespace Admin\Services\Image\Drivers;

class Imagemagick extends AbstractDriver
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
        return $this->process('rotate');
    }

    public function watermark()
    {
        // Not implemented in original code
        return false;
    }

    protected function process($action = 'resize')
    {
        if ($this->parent->library_path === '')
        {
            $this->parent->set_error('imglib_libpath_invalid');
            return false;
        }

        if ( ! preg_match('/convert$/i', (string)$this->parent->library_path))
        {
            $this->parent->library_path = rtrim((string)$this->parent->library_path, '/').'/convert';
        }

        $cmd = $this->parent->library_path.' -quality '.$this->parent->quality;

        if ($action === 'crop')
        {
            $cmd .= ' -crop '.$this->parent->width.'x'.$this->parent->height.'+'.$this->parent->x_axis.'+'.$this->parent->y_axis;
        }
        elseif ($action === 'rotate')
        {
            $cmd .= ($this->parent->rotation_angle === 'hor' OR $this->parent->rotation_angle === 'vrt')
                    ? ' -flop'
                    : ' -rotate '.$this->parent->rotation_angle;
        }
        else // Resize
        {
            if($this->parent->maintain_ratio === true)
            {
                $cmd .= ' -resize '.$this->parent->width.'x'.$this->parent->height;
            }
            else
            {
                $cmd .= ' -resize '.$this->parent->width.'x'.$this->parent->height.'\!';
            }
        }

        $cmd .= ' '.escapeshellarg($this->parent->full_src_path).' '.escapeshellarg($this->parent->full_dst_path).' 2>&1';

        $retval = 1;
        @exec($cmd, $output, $retval);

        if ($retval > 0)
        {
            $this->parent->set_error('imglib_image_process_failed');
            return false;
        }

        chmod($this->parent->full_dst_path, $this->parent->file_permissions);
        return true;
    }
}
