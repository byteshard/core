<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use byteShard\Combo\Option;
use byteShard\Internal\SimpleXML;
use SimpleXMLElement;

/**
 * Class BSCombo
 */
abstract class Combo
{
    /** @var Option[]|string[] */
    private array         $options               = [];
    private string        $encoding              = 'utf-8';
    protected string      $output_charset        = 'utf-8';
    private static string $output_charset_static = 'utf-8';
    private array         $content               = [];
    private string        $container             = '';
    private ?object       $selectedOption        = null;
    private array         $parameters            = [];
    private string        $cellNonce             = '';

    /** @internal */
    public function setCellNonce(string $nonce): self
    {
        $this->cellNonce = $nonce;
        return $this;
    }

    abstract function defineComboOptions(): void;

    /**
     * @param Option ...$options
     * @return $this
     * @API
     */
    public function addOptions(Option ...$options): self
    {
        foreach ($options as $option) {
            $this->options[] = $option;
        }
        return $this;
    }

    /** @throws \Exception */
    public function getComboContents(string $container = ''): string
    {
        $this->container = $container;
        $this->defineComboOptions();
        SimpleXML::initializeDecode();
        $xmlParent = new SimpleXMLElement('<?xml version="1.0" encoding="'.$this->encoding.'" ?><complete/>');
        foreach ($this->options as $option) {
            $option->getXMLElement($xmlParent, false, false, $this->cellNonce);
        }
        return SimpleXML::asString($xmlParent);
    }

    public function setSelectedOption(object $id): void
    {
        $this->selectedOption = $id;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getContainerClass(): string
    {
        return $this->container;
    }


    /////// OLD METHODS, CHECK IF NEEDED ////////

    /**
     * @param Option ...$options
     * @return $this
     */
    public function setOptions(Combo\Option ...$options): self
    {
        foreach ($options as $option) {
            $this->options[] = $option;
        }
        return $this;
    }

    /**
     * @API
     */
    public function setQuery(string $query): self
    {
        $this->options[] = $query;
        return $this;
    }

    /**
     * @return Option[]
     */
    public function getContent(): array
    {
        if (!empty($this->content)) {
            return $this->content;
        }
        foreach ($this->options as $option) {
            if ($option instanceof Combo\Option) {
                $this->content[] = $option;
            } elseif (is_string($option)) {
                try {
                    $tmp = Database::getArray($option);
                } catch (Exception) {
                    $tmp = [];
                }
                foreach ($tmp as $val) {
                    $this->content[] = new Combo\Option($val->Value, (isset($val->Text)) ? $val->Text : null, (isset($val->Selected) && ($val->Selected == 1 || $val->Selected === true)), (isset($val->Image)) ? $val->Image : null);
                }
            }
        }
        return $this->content;
    }

    /**
     * @return array
     */
    public function getIdReferences(): array
    {
        $options      = $this->getContent();
        $idReferences = [];
        foreach ($options as $option) {
            $idReferences[$option->getValue()] = $option->getText();
        }
        return $idReferences;
    }

    /**
     * called in: FormObject
     * @session none
     * @param ?SimpleXMLElement $xmlParent
     * @param bool $textAsXMLAttribute
     * @internal
     */
    public function getXMLElement(?SimpleXMLElement $xmlParent, bool $textAsXMLAttribute = true): void
    {
        $options = $this->getContent();
        foreach ($options as $option) {
            if ($option instanceof Combo\Option) {
                $option->getXMLElement($xmlParent, $textAsXMLAttribute, false, $this->cellNonce);
            }
        }
    }

    /**
     * @session none
     * @throws \Exception
     * @internal
     */
    public function getXML(): string
    {
        $options = $this->getContent();
        if (!empty($options)) {
            SimpleXML::initializeDecode();
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="'.$this->output_charset.'" ?><complete/>');
            foreach ($options as $option) {
                if ($option instanceof Combo\Option) {
                    $option->getXMLElement($xml, false, false, $this->cellNonce);
                }
            }
            return SimpleXML::asString($xml);
        }
        return '';
    }

    /**
     * called in Grid
     *
     * @session none
     * @throws \Exception
     * @internal
     */
    static public function getXMLString(array $content): string
    {
        SimpleXML::initializeDecode();
        $parentXMLObj = new SimpleXMLElement('<?xml version="1.0" encoding="'.self::$output_charset_static.'" ?><complete/>');
        foreach ($content as $itemDesc => $itemContent) {
            switch ($itemDesc) {
                //TODO: align Options array with DHTMLXCombo: in XML on first level per OPTION
                case 'options':
                    foreach ($itemContent as $optionData) {
                        $row = SimpleXML::addChild($parentXMLObj, 'option', $optionData['text']);
                        foreach ($optionData as $attrName => $attrVal) {
                            if ($attrName !== 'text') {
                                SimpleXML::addAttribute($row, $attrName, $attrVal);
                            }
                        }
                    }
                    break;
                case 'note':
                    foreach ($itemContent as $noteAttr => $noteData) {
                        SimpleXML::addAttribute(SimpleXML::addChild($parentXMLObj, $itemDesc), $noteAttr, $noteData);
                    }
                    break;
                default:
                    SimpleXML::addChild($parentXMLObj, $itemDesc, $itemContent);
                    break;
            }
        }
        return SimpleXML::asString($parentXMLObj);
    }

    protected function getId(string $id): mixed
    {
        if ($this->selectedOption !== null && property_exists($this->selectedOption, $id)) {
            return $this->selectedOption->{$id};
        }
        return Session::getID($id, $this->getContainerClass());
    }
}
