<?php

namespace byteShard\Config;

use byteShard\Internal\Schema\DB\UserTable;

interface OverrideDataModelInterface
{
    public function getOverrideDefinitions(): UserTable;
}