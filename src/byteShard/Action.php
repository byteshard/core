<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

/**
 * Class Action
 * @package byteShard
 */
class Action {
    /**
     * convenience method to get the client response for one or more actions
     * @param Cell $cell
     * @param null $id
     * @param Internal\Action ...$actions
     * @return array<string,int|array>
     */
    public static function getClientResponse(Cell $cell, $id = null, Internal\Action ...$actions): array {
        $result = [];
        $merge_array = [];
        foreach ($actions as $action) {
            $merge_array[] = $action->getResult($cell, $id);
        }
        $result = array_merge_recursive($result, ...$merge_array);
        $result['state'] = array_key_exists('state', $result) ? is_array($result['state']) ? min(2, min($result['state'])) : $result['state'] : 2;
        return $result;
    }
}
