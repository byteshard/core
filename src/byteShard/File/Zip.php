<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File;

use byteShard\Enum\FileType;

/**
 * Class Text
 * @package byteShard\File
 */
class Zip implements FileInterface
{
    private string $fileName    = '';
    private string $fileContent = '';

    /**
     * set the contents of the file
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->fileContent = $content;
    }

    /**
     * the default name for the downloaded file
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->fileName = $name;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return FileType::getContentType(FileType::ZIP->value);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (!empty($this->fileName)) ? $this->fileName : 'byteShard';
    }

    /**
     * @return int|null
     */
    public function getContentLength(): ?int
    {
        return strlen($this->fileContent);
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [];
    }

    public function getContent(): void
    {
        file_put_contents('php://output', $this->fileContent);
    }

    public function getFileExtension(): string
    {
        return FileType::ZIP->value;
    }
}
