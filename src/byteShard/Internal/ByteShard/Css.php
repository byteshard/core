<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\ByteShard;

/**
 * Class Css
 * @package byteShard\Internal\ByteShard
 */
class Css extends Asset
{
    private string $cssPath;

    /**
     * Css constructor.
     * @param string $cssPath
     */
    public function __construct(string $cssPath)
    {
        $this->cssPath = $this->clean($cssPath);
    }

    public function includeCss(array $files, string $cssSubDirectory = '', string $target = 'app'): array
    {
        $result          = [];
        $cssSubDirectory = $this->clean($cssSubDirectory);
        $paths[]         = BS_WEB_ROOT_DIR.'/'.$target;
        $paths[]         = $this->cssPath;
        $paths[]         = $cssSubDirectory;
        $path            = implode('/', array_filter($paths)).'/';
        foreach ($files as $filename) {
            $result[] = '<link rel="stylesheet" type="text/css" href="'.$path.$filename.'">';
        }
        return $result;
    }

    public static function includeCssFullPath(array $files): array
    {
        $result = [];
        $path   = BS_WEB_ROOT_DIR.'/';
        foreach ($files as $filename) {
            $result[] = '<link rel="stylesheet" type="text/css" href="'.$path.$filename.'">';
        }
        return $result;
    }
}
