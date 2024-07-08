<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Sanitizer;

use byteShard\Enum\FileType;
use byteShard\Exception;
use byteShard\Internal\Debug;

/**
 * Class File
 * @package byteShard\Internal\Sanitizer
 */
class File
{
    private string  $filename          = '';
    private array   $allowedFileTypes  = [];
    private string  $temporaryFilename = '';
    private string  $httpHeaderType    = '';
    private bool    $error             = false;
    private ?string $targetFilename;
    private ?string $targetPath;
    private array   $mimeTypes;
    private ?string $serverFileFQFN    = null;
    private ?string $serverFileName    = null;
    private ?string $serverFilePath    = null;

    /**
     * @throws Exception
     */
    public function __construct(array $fileArray, array $fileTypes, ?string $targetPath, ?string $targetFilename)
    {
        if (isset($fileArray['error']) && $fileArray['error'] === 0) {
            if (isset($fileArray['name'])) {
                $this->filename = $fileArray['name'];
            } else {
                $this->error = true;
            }
            if (isset($fileArray['tmp_name'])) {
                $this->temporaryFilename = $fileArray['tmp_name'];
            } else {
                $this->error = true;
            }
            if (isset($fileArray['type'])) {
                $this->httpHeaderType = $fileArray['type'];
            } else {
                $this->error = true;
            }
            if (!isset($fileArray['size'])) {
                $this->error = true;
            }
            $this->mimeTypes = FileType::getFileTypes();
            $this->setAllowedFileTypes($fileTypes);
            $this->targetFilename = $targetFilename;
            $this->targetPath     = ($targetPath !== null) ? str_replace('/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $targetPath)) : null;
            $this->sanitize();
        } else {
            $this->error = true;
        }
        if ($this->error === true) {
            $this->deleteTemporaryFile();
        }
    }

    public function hasErrors(): bool
    {
        return $this->error;
    }

    public function getServerFilename(): ?string
    {
        return $this->serverFileName;
    }

    public function getServerFilepath(): ?string
    {
        return $this->serverFilePath;
    }

    public function getServerFileFQFN(): ?string
    {
        return $this->serverFileFQFN;
    }

    /**
     * @throws Exception
     */
    private function sanitize(): void
    {
        if ($this->error === false) {
            $extension = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
            if ($this->checkFileExtensionsAndMimeType($extension) === true) {
                if (!empty($this->targetPath)) {
                    $filename = pathinfo($this->targetFilename ?? $this->filename, PATHINFO_FILENAME);
                    $filename = $this->sanitizeFileName($filename);
                    $this->moveFile($filename, $extension);
                } else {
                    $this->serverFileFQFN = $this->temporaryFilename;
                    $this->serverFileName = basename($this->temporaryFilename);
                    $this->serverFilePath = dirname($this->temporaryFilename);
                }
            } else {
                $this->error = true;
            }
        }
    }

    /**
     * @param string $filename
     * @param string $extension
     * @throws Exception
     */
    private function moveFile(string $filename, string $extension): void
    {
        $path = rtrim(BS_FILE_PRIVATE_ROOT, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.trim($this->targetPath, " \t\n\r\0\x0B/\\").DIRECTORY_SEPARATOR;
        if (!is_writable($path)) {
            Debug::critical('Upload directory not writable: '.$path);
        }
        if (!file_exists($path.$filename.'.'.$extension)) {
            if (move_uploaded_file($this->temporaryFilename, $path.$filename.'.'.$extension) === false) {
                Debug::error('Could not move file');
            }
            $this->serverFileName = $filename.'.'.$extension;
        } else {
            $i = 1;
            while (true) {
                if ($this->checkFilenameLength($filename.'_'.$i) === true && !file_exists($path.$filename.'_'.$i.'.'.$extension)) {
                    if (move_uploaded_file($this->temporaryFilename, $path.$filename.'_'.$i.'.'.$extension) === false) {
                        Debug::error('Could not move file');
                    }
                    $this->serverFileName = $filename.'_'.$i.'.'.$extension;
                    break;
                }
                $i++;
            }
        }
        $this->serverFilePath = $path;
        $this->serverFileFQFN = $this->serverFilePath.DIRECTORY_SEPARATOR.$this->serverFileName;
    }

    private function sanitizeFileName($filename): string
    {
        // Convert the filename to UTF-8
        $filename = mb_convert_encoding($filename, 'UTF-8', 'auto');
        // replace invalid characters with underscores
        $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
        // replace underscore or blank repetitions
        $filename = preg_replace('/([_ ])\1+/', '\1', $filename);

        // Get the file extension
        $pathInfo                 = pathinfo($filename);
        $extension                = isset($pathInfo['extension']) ? '.'.$pathInfo['extension'] : '';
        $filenameWithoutExtension = $pathInfo['filename'];

        // Limit the length of the filename, keeping the extension
        $maxLength = 255 - strlen($extension);
        if (strlen($filenameWithoutExtension) > $maxLength) {
            $filenameWithoutExtension = substr($filenameWithoutExtension, 0, $maxLength);
        }
        return $filenameWithoutExtension.$extension;
    }

    /**
     * @throws Exception
     */
    private function checkFilenameLength(string $filename): bool
    {
        if (strlen($filename) > 250) {
            $exception = new Exception('Filename too long');
            $exception->setLocaleToken('byteShard.upload.sanitizer.fileName.length');
            $this->deleteTemporaryFile();
            throw $exception;
        }
        return true;
    }

    /**
     * @param string $extension
     * @return bool
     * @throws Exception
     */
    private function checkFileExtensionsAndMimeType(string $extension): bool
    {
        $mimeType = null;
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        if (!empty($finfo)) {
            $mimeType = finfo_file($finfo, $this->temporaryFilename);
        }
        if (array_key_exists($extension, $this->allowedFileTypes)) {
            if ($mimeType !== null) {
                if (in_array($mimeType, $this->allowedFileTypes[$extension]['finfo']) && in_array($this->httpHeaderType, $this->allowedFileTypes[$extension]['http_header'])) {
                    return true;
                }
                if (!in_array($mimeType, $this->allowedFileTypes[$extension]['finfo']) && !in_array($this->httpHeaderType, $this->allowedFileTypes[$extension]['http_header'])) {
                    $exception = new Exception('Mime types do not match allowed mime types. File mime type = '.$mimeType.' - Expected mime type = '.implode(', ', $this->allowedFileTypes[$extension]['finfo']).'. Client mime type = '.$this->httpHeaderType.' - Expected mime type = '.implode(', ', $this->allowedFileTypes[$extension]['http_header']));
                    $exception->setLocaleToken('byteShard.upload.sanitizer.mimeType.invalid.1');
                    $this->deleteTemporaryFile();
                    throw $exception;
                }
                if (!in_array($mimeType, $this->allowedFileTypes[$extension]['finfo'])) {
                    $exception = new Exception('Mime type does not match allowed mime type. File mime type = '.$mimeType.' - Expected mime type = '.implode(', ', $this->allowedFileTypes[$extension]['finfo']));
                    $exception->setLocaleToken('byteShard.upload.sanitizer.mimeType.invalid.2');
                    $this->deleteTemporaryFile();
                    throw $exception;
                }
                $exception = new Exception('Client Mime type does not match allowed mime type. Client mime type = '.$this->httpHeaderType.' - Expected mime type = '.implode(', ', $this->allowedFileTypes[$extension]['http_header']));
                $exception->setLocaleToken('byteShard.upload.sanitizer.mimeType.invalid.3');
                $this->deleteTemporaryFile();
                throw $exception;
            }
            $exception = new Exception('Mime type could not be determined');
            $exception->setLocaleToken('byteShard.upload.sanitizer.mimeType.unidentified');
            $this->deleteTemporaryFile();
            throw $exception;
        }
        $exception = new Exception('File extension "'.$extension.'" is not allowed. File mime type: '.$mimeType.' - HTTP header type: '.$this->httpHeaderType);
        $exception->setLocaleToken('byteShard.upload.sanitizer.fileType.invalid');
        $this->deleteTemporaryFile();
        throw $exception;
    }

    private function setAllowedFileTypes(array $fileTypes): void
    {
        foreach ($fileTypes as $fileType) {
            $enum = FileType::tryFrom($fileType);
            if ($enum !== null) {
                $this->allowedFileTypes[$enum->value] = $this->mimeTypes[$enum->value];
            }
        }
    }

    /**
     * remove the uploaded file from the temp dir
     */
    private function deleteTemporaryFile(): void
    {
        if (!empty($this->temporaryFilename)) {
            unlink($this->temporaryFilename);
            $this->temporaryFilename = '';
        }
    }


}
