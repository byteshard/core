<?php

namespace byteShard\Config;

use byteShard\DataModelInterface;

interface CustomDataModelInterface
{
    public function getByteShardDataModel(): DataModelInterface;
}