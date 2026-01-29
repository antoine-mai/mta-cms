<?php namespace Admin\Services\Image\Drivers;

class Netpbm extends AbstractDriver
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

        switch ($this->parent->image_type)
        {
            case 1 :
                $cmd_in		= 'giftopnm';
                $cmd_out	= 'ppmtogif';
                break;
            case 2 :
                $cmd_in		= 'jpegtopnm';
                $cmd_out	= 'ppmtojpeg';
                break;
            case 3 :
                $cmd_in		= 'pngtopnm';
                $cmd_out	= 'ppmtopng';
                break;
        }

        if ($action === 'crop')
        {
            $cmd_inner = 'pnmcut -left '.$this->parent->x_axis.' -top '.$this->parent->y_axis.' -width '.$this->parent->width.' -height '.$this->parent->height;
        }
        elseif ($action === 'rotate')
        {
            switch ($this->parent->rotation_angle)
            {
                case 90:	$angle = 'r270';
                    break;
                case 180:	$angle = 'r180';
                    break;
                case 270:	$angle = 'r90';
                    break;
                case 'vrt':	$angle = 'tb';
                    break;
                case 'hor':	$angle = 'lr';
                    break;
            }
            $cmd_inner = 'pnmflip -'.$angle.' ';
        }
        else // Resize
        {
            $cmd_inner = 'pnmscale -xysize '.$this->parent->width.' '.$this->parent->height;
        }

        $cmd = $this->parent->library_path.$cmd_in.' '.escapeshellarg($this->parent->full_src_path).' | '.$cmd_inner.' | '.$cmd_out.' > '.$this->parent->dest_folder.'netpbm.tmp';

        $retval = 1;
        @exec($cmd, $output, $retval);

        if ($retval > 0)
        {
            $this->parent->set_error('imglib_image_process_failed');
            return false;
        }

        copy($this->parent->dest_folder.'netpbm.tmp', $this->parent->full_dst_path);
        unlink($this->parent->dest_folder.'netpbm.tmp');
        chmod($this->parent->full_dst_path, $this->parent->file_permissions);
        return true;
    }
}
