<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File;

use byteShard\Enum\FileType;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;

class Ppt implements FileInterface
{
    private string           $fileName    = '';
    private ?PhpPresentation $fileContent = null;

    /**
     * set the contents of the file
     * @param PhpPresentation $content
     */
    public function setContent(PhpPresentation $content): void
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
     * @inheritDoc
     */
    public function getContentType(): string
    {
        return FileType::getContentType(FileType::PPTX->value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (!empty($this->fileName)) ? $this->fileName : 'byteShard';
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
        if ($this->fileContent !== null) {
            $writer = IOFactory::createWriter($this->fileContent);
            $writer->save('php://output');
        }
    }

    /**
     * @inheritDoc
     */
    public function getFileExtension(): string
    {
        return FileType::PPTX->value;
    }
}