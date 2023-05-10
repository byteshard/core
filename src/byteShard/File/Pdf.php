<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File;

use byteShard\Enum\FileType;

class Pdf implements FileInterface
{

    /** @var string */
    private string $fileName = '';

    private string $fileContent;

    /**
     * set the contents of the file
     */
    public function setContent(string $content)
    {
        $this->fileContent = $content;
    }

    /**
     * the default name for the downloaded file
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->fileName = $name;
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): string
    {
        return FileType::getContentType(FileType::PDF->value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (!empty($this->fileName)) ? $this->fileName : 'byteShard.pdf';
    }

    /**
     * @inheritDoc
     */
    public function getContentLength(): ?int
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getContent(): void
    {
        file_put_contents('php://output', $this->fileContent);
    }

    /**
     * @inheritDoc
     */
    public function getFileExtension(): string
    {
        return FileType::PDF->value;
    }
}