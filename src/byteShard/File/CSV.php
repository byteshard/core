<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File;

use byteShard\Enum\FileType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CSVWriter;

/**
 * Class CSV
 * @package byteShard\File
 */
class CSV implements FileInterface
{
    private string       $fileName    = '';
    private ?Spreadsheet $fileContent = null;

    /**
     * set the contents of the file
     * @param Spreadsheet $content
     */
    public function setContent(Spreadsheet $content): void
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

    public function getContentType(): string
    {
        return FileType::getContentType(FileType::CSV->value);
    }

    public function getName(): string
    {
        return (!empty($this->fileName)) ? $this->fileName : 'byteShard.csv';
    }

    public function getFileExtension(): string
    {
        return FileType::CSV->value;
    }

    public function getContentLength(): ?int
    {
        return null;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getContent(): void
    {
        if ($this->fileContent !== null) {
            $writer = new CSVWriter($this->fileContent);
            $writer->setDelimiter(';');
            $writer->save('php://output');
        }
    }
}
