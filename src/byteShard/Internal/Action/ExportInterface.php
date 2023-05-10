<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Action;

use byteShard\Enum\Export\ExportType;
use byteShard\ID\ID;

/**
 * Interface ExportInterface
 * @package byteShard\Internal\Action
 */
interface ExportInterface {
    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * @internal
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param bool $bool
     * @return $this
     */
    public function setUseDateInFilename(bool $bool = true): self;

    /**
     * @param bool $bool
     * @return $this
     */
    public function setUseApplicationNameInFilename(bool $bool = true): self;

    /**
     * @internal
     * @return bool
     */
    public function getUseDateInFilename(): bool;

    /**
     * @internal
     * @return bool
     */
    public function getUseApplicationNameInFilename(): bool;

    /**
     * @internal
     * @return ExportType
     */
    public function getType(): ExportType;

    /**
     * @internal
     * @return null|string
     */
    public function getContentID(): ?string;

    /**
     * @internal
     * @return int
     */
    public function getTimeout(): int;

    /**
     * @internal
     * @param ID $id
     * @param string $eventId
     * @return void
     */
    public function setEventId(ID $id, string $eventId): void;
}
