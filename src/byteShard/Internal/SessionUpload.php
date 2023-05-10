<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\Cell;

class SessionUpload
{
    private array $cells         = [];
    private array $uploadedFiles = [];

    public function getUploadId(Cell $cell, ?string $uploadControlName, ?string $encrypted_client_name, array $fileTypeArray = [], string $method = '', string $targetFilename = '', string $targetPath = '', bool $clearAfterUpload = false, string $cryptoKey = ''): ?string
    {
        //get ID
        $containerID = $cell->containerId();
        $cellID      = $cell->cellId();
        // $id will be used in $_GET['type']

        $this->unlinkFilesFromPreviousRequest($containerID, $cellID, $uploadControlName);

        $id = $this->getEncryptedId($cell, $uploadControlName, $clearAfterUpload, $fileTypeArray, $method, $targetFilename, $targetPath, $cryptoKey);
        //$id                                                                           = md5($containerID.$cellID.$upload_control_name.date('YmdHis', time()));
        $this->cells[$containerID][$cellID][$uploadControlName] = $id;
        if (!isset($this->uploadedFiles[$id])) {
            $this->uploadedFiles[$id] = [];
        }
        return $id;
    }

    private function unlinkFilesFromPreviousRequest(string $containerID, string $cellID, string $uploadControlName): void
    {
        if (isset($this->cells[$containerID], $this->cells[$containerID][$cellID][$uploadControlName]) && !empty($this->cells[$containerID][$cellID][$uploadControlName])) {
            $previousId = $this->cells[$containerID][$cellID][$uploadControlName];
            if (isset($this->uploadedFiles[$previousId])) {
                // unlink all files from previous request which have not been moved / processed. This happens when a session expires or a user navigates away from the Form where the upload was defined
                foreach ($this->uploadedFiles[$previousId] as $uploadData) {
                    if (isset($uploadData['file_name'], $uploadData['path']) && file_exists(rtrim(rtrim($uploadData['path'], '/'), '\\').DIRECTORY_SEPARATOR.$uploadData['file_name'])) {
                        unlink(rtrim(rtrim($uploadData['path'], '/'), '\\').DIRECTORY_SEPARATOR.$uploadData['file_name']);
                    }
                }
                unset($this->uploadedFiles[$previousId]);
            }
            unset($this->cells[$containerID][$cellID][$uploadControlName]);
        }
    }

    private function getEncryptedId(Cell $cell, string $uploadControlName, bool $clearAfterUpload, array $fileTypeArray, string $method, string $targetFilename, string $targetPath, string $cryptoKey): string
    {

        $message = [
            '!#c' => $cell->getContentClass(),
            '!#o' => $uploadControlName,
            '!#u' => $clearAfterUpload,
            '!#m' => $method,
            '!#n' => $targetFilename,
            '!#p' => $targetPath,
            '!#i' => $cell->getNewId()->getEncodedCellId()
        ];
        if (!empty($fileTypeArray)) {
            $message['!#f'] = $fileTypeArray;
        }
        $message = (object)array_filter($message);
        return urlencode(\byteShard\Session::encrypt(json_encode($message), $cell->getNonce()));
    }

    /**
     * @param string $uploadObjectId
     * @param string $fileName
     * @param string $path
     * @param string $clientFileName
     */
    public function setUploadFileData(string $uploadObjectId, string $fileName, string $path, string $clientFileName): void
    {
        if (isset($this->uploadedFiles[$uploadObjectId])) {
            $index = count($this->uploadedFiles[$uploadObjectId]) + 1;
            foreach ($this->uploadedFiles[$uploadObjectId] as $key => $file) {
                if ($file['client_file_name'] === $clientFileName) {
                    unlink($file['path'].DIRECTORY_SEPARATOR.$file['file_name']);
                    $index = $key;
                    break;
                }
            }
            $this->uploadedFiles[$uploadObjectId][$index] = [
                'file_name'        => $fileName,
                'path'             => $path,
                'client_file_name' => $clientFileName
            ];
        }
    }
}