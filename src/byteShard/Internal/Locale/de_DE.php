<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Locale;
use byteShard\Locale;

/**
 * Class de_DE
 * @package byteShard\Internal\Locale
 *
 * contains all texts for the framework in german (germany)
 * Don't use the same token over and over again.
 * Make specific tokens for every single message and rather refer to the default message
 * That way in debug mode the token can be returned and is easy to find
 *
 * prepend an array dimension named "debug" for a message that will be displayed in the browser while debug is true
 * e.g.:
 * self::$locale['test'] = 'something happened';
 * self::$locale['debug']['test'] = "the coder didn't specify xyz";
 */
class de_DE extends Locale {

}
