<?php namespace Admin\Services;
/**
 * Download Class
 *
 * Provides functions to force file downloads.
**/
class Download implements Interfaces\DownloadInterface
{
    /**
     * Force Download
     *
     * Generates a server response which forces a download to happen.
     *
     * @param	string	$filename	Filename
     * @param	mixed	$data		File data
     * @param	bool	$set_mime	Whether to try to send the actual MIME type
     * @return	void
     */
    public function force($filename = '', $data = '', $set_mime = false)
    {
        if ($filename === '' OR $data === '')
        {
            return;
        }
        elseif ($data === null)
        {
            if ( ! @is_file($filename) OR ($filesize = @filesize($filename)) === false)
            {
                return;
            }

            $filepath = $filename;
            $filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
            $filename = end($filename);
        }
        else
        {
            $filesize = strlen($data);
        }

        // Set the default MIME type to send
        $mime = 'application/octet-stream';

        $x = explode('.', $filename);
        $extension = end($x);

        if ($set_mime === true)
        {
            if (count($x) === 1 OR $extension === '')
            {
                /* If we're going to try to set the MIME type
                 * we need at least a file extension.
                 */
                return;
            }

            $mimes =& getMimes();

            // Only change the default MIME if we can find one
            if (isset($mimes[$extension]))
            {
                $mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
            }
        }

        /* It was reported that some Android versions
         * return a wrong type if upper case extensions are used.
         */
        if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT']))
        {
            $x[count($x) - 1] = strtoupper($extension);
            $filename = implode('.', $x);
        }

        if ($data === null && ($fp = @fopen($filepath, 'rb')) === false)
        {
            return;
        }

        // Clean output buffer
        if (ob_get_level() !== 0 && @ob_end_clean() === false)
        {
            @ob_clean();
        }

        // Generate the server headers
        header('Content-Type: '.$mime);
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.$filesize);
        header('Cache-Control: private, no-transform, no-store, must-revalidate');

        // If we have data, just echo it and exit
        if ($data !== null)
        {
            exit($data);
        }

        // If we are reading a file, echo it in chunks
        while ( ! feof($fp) && ($data = fread($fp, 1048576)) !== false)
        {
            echo $data;
        }

        fclose($fp);
        exit;
    }
}
