namespace App/Services

class Image
{
    private function init($path)
    {
        $src = imagecreatefromjpeg($path);
        // get mime type of file
        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
        switch (strtolower($mime)) {
            case 'image/png':
            case 'image/x-png':
                $src = @imagecreatefrompng($path);
                break;
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $src = @imagecreatefromjpeg($path);
                if (!$src) {
                    $core= @imagecreatefromstring(file_get_contents($path));
                }
                break;
            case 'image/gif':
                $src = @imagecreatefromgif($path);
                break;
            case 'image/webp':
            case 'image/x-webp':
                if (function_exists('imagecreatefromwebp')) {
                    $src = @imagecreatefromwebp($path);
                }
                break;
        }
        if (empty($src)) throw new error;
        return $src;
    }

    public function resize_image($path, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($path);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = $this->init($path);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        return $dst;
    }
}
