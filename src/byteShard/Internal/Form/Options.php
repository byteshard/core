<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Combo;
use byteShard\Exception;

/**
 * Trait Options
 * @package byteShard\Form\Internal
 * @property array $data
 */
trait Options
{
    // Protected because the trait is used in the child, but needs to be accessed in the parent
    protected array $options = [];

    /**
     * @param $options
     * @return $this
     * @throws Exception
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setOptions(Combo|Combo\Option|array $options): self
    {
        //if ($options instanceof Combo) {
        //    $this->options = $options;
        //} elseif (($options instanceof Combo\Option)) {
        if (($options instanceof Combo\Option)) {
            $this->options[] = $options;
        } else {
            foreach ($options as $option) {
                if ($option instanceof Combo\Option) {
                    $this->options[] = $option;
                } else {
                    print_r(debug_backtrace());
                    $e = new Exception(__METHOD__.": Method only accepts objects of type byteShard\\Combo, byteShard\\Combo\\Option or an array of byteShard\\Combo\\Option. Input was '".gettype($options)."'");
                    $e->setLocaleToken('byteShard.form.invalidArgument.setOptions.options');
                    throw $e;
                }
            }
        }
        return $this;
    }
}
