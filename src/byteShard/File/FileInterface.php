<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File;

/**
 * Interface FileInterface
 * @package byteShard\File
 */
interface FileInterface {
    /**
     * set the contents of the file
     * @param $content
     */
    //public function setContent($content);

    /**
     * the default name for the downloaded file
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return int|null
     */
    public function getContentLength(): ?int;

    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @return void
     */
    public function getContent(): void;

    /**
     * @return string
     */
    public function getFileExtension(): string;
}
