<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Upload;

use Exception;

class File
{
    private string $fileName;
    private string $path;
    private string $fqfn;
    private string $extension;
    private string $name;
    private string $clientName;

    /**
     * @param string $fqfn
     * @param string $clientName
     * @throws Exception
     */
    public function __construct(string $fqfn, string $clientName = '')
    {
        if (str_starts_with($fqfn, '!#up=')) {
            $uploadControlResponse = json_decode(substr($fqfn, 5));
            $fqfn                  = $uploadControlResponse->p.'/'.$uploadControlResponse->f;
            $clientName            = $uploadControlResponse->c;
        }
        if (file_exists($fqfn)) {
            $pathInfo        = pathinfo($fqfn);
            $this->path      = $pathInfo['dirname'];
            $this->fileName  = $pathInfo['basename'];
            $this->extension = $pathInfo['extension'];
            $this->name      = $pathInfo['filename'];
            $this->fqfn      = $fqfn;
        } else {
            throw new Exception('Uploaded file ('.$fqfn.') does not exist');
        }
        $this->clientName = $clientName;
    }

    /** @API */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /** @API */
    public function getPath(): string
    {
        return $this->path;
    }

    /** @API */
    public function getFQFN(): string
    {
        return $this->fqfn;
    }

    /** @API */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /** @API */
    public function getName(): string
    {
        return $this->name;
    }

    /** @API */
    public function getClientFileName(): string
    {
        return $this->clientName;
    }
}
