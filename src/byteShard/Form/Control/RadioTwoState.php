<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Enum;
use byteShard\Form\Enum\Label\Position;
use byteShard\Internal\Form;

/**
 * Class Radio
 * @package byteShard\Form\Control
 */
class RadioTwoState extends Form\FormObject implements Form\CollectionInterface
{
    private Block         $block_control;
    private LabelAdvanced $label_control;

    private Radio $false_control;

    private Radio $true_control;

    private bool $info = false;

    private bool $hidden = false;

    public function __construct($id)
    {
        parent::__construct($id);
        $this->block_control = new Block();
        if (is_string($id)) {
            $this->block_control->setName($id);
        }
        $this->label_control = new LabelAdvanced($this->getName().'Label');
        $this->label_control->setLocaleName($this->getName());
        $this->false_control = new Radio($this->getName());
        $this->false_control->setValue('0');
        $this->false_control->setLabelWidth();
        $this->false_control->setDBColumnType(Enum\DB\ColumnType::BOOLEAN);
        $this->false_control->setPosition(Position::RIGHT);
        $this->false_control->setOffsetLeft(3);
        $this->true_control = new Radio($this->getName());
        $this->true_control->setValue('1');
        $this->true_control->setLabelWidth();
        $this->true_control->setDBColumnType(Enum\DB\ColumnType::BOOLEAN);
        $this->true_control->setPosition(Position::RIGHT);
    }

    /**
     * @return Radio
     */
    public function getTrueRadioControl(): Radio
    {
        return $this->true_control;
    }

    /**
     * @return Radio
     */
    public function getFalseRadioControl(): Radio
    {
        return $this->false_control;
    }

    public function setRadioPosition(): void
    {

    }

    public function setLocaleBaseToken(string $token): void
    {
        $this->token = $token;
    }

    public function setHidden(bool $hidden = true): self
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function setInfo(bool $info = true): self
    {
        $this->info = $info;
        return $this;
    }

    public function getElements(): array
    {
        if ($this->hidden === true) {
            $this->block_control->setHidden(true);
        }
        $this->block_control->addFormObject($this->label_control);
        $this->label_control->setOffsetLeft(0);
        if ($this->info === true) {
            $this->label_control->setInfo();
        }
        $this->block_control->addFormObject(new NewColumn());
        $this->block_control->addFormObject($this->false_control);
        $this->block_control->addFormObject(new NewColumn());
        $this->block_control->addFormObject($this->true_control);
        return [$this->block_control];
    }
}
