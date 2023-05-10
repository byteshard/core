<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\ByteShard;

/**
 * Class Javascript
 * @package byteShard\Internal\ByteShard
 */
class Javascript extends Asset
{
    private string $jsPath;

    /**
     * Javascript constructor.
     * @param string $jsPath
     */
    public function __construct(string $jsPath = '')
    {
        $this->jsPath = $this->clean($jsPath);
    }

    public function includeJavascripts(array $filenames, string $jsSubDir = '', string $target = 'app'): array
    {
        $jsSubDir = $this->clean($jsSubDir);
        // define the path to the js from the webroot
        $publicPaths[] = BS_WEB_ROOT_DIR.'/'.$target;
        $publicPaths[] = $this->jsPath;
        $publicPaths[] = $jsSubDir;
        $serverPaths[] = BS_FILE_PUBLIC_ROOT.'/'.$target;
        $serverPaths[] = $this->jsPath;
        $serverPaths[] = $jsSubDir;
        // the complete path to the js when it's accessed from the web
        $publicPathToScript = implode('/', array_filter($publicPaths)).'/';
        // the complete path to the js on the filesystem. This is needed for file_exists
        $systemPathToScript = implode(DIRECTORY_SEPARATOR, array_filter($serverPaths)).DIRECTORY_SEPARATOR;

        $result = [];
        foreach ($filenames as $filename) {
            $filename = $this->clean($filename);
            if (file_exists($systemPathToScript.$filename)) {
                $result[] = '<script src="'.$publicPathToScript.$filename.'?ver='.filemtime($systemPathToScript.$filename).'"></script>';
            } else {
                $result[] = '<script src="'.$publicPathToScript.'jserror.js?script='.$filename.'"></script>';
            }
        }
        return $result;
    }

    public static function includeJavascriptFullPath(array $files): array
    {
        $result = [];
        $path   = BS_WEB_ROOT_DIR.'/';
        foreach ($files as $filename) {
            $result[] = '<script src="'.$path.$filename.'"></script>';
        }
        return $result;
    }
}
