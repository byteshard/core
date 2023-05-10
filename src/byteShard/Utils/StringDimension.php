<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Utils;

use byteShard\Utils\StringDimension\Line;

class StringDimension
{
    private float  $fontSize;
    private string $fontPath;
    private array  $linebreakCharacters = ["\r\n", "\n\r", "\r", "\n", '<br>', '<br />', '<br/>'];

    /**
     * @param float $fontSize font size in px
     * @param string $pathToTrueTypeFont
     */
    public function __construct(float $fontSize, string $pathToTrueTypeFont)
    {
        $this->fontSize = SizeConversion::pxToPt($fontSize);
        $this->fontPath = $pathToTrueTypeFont;
    }

    /**
     * @API
     * @param string $fontPathName path to .ttf file to be used for measure
     * @return $this
     */
    public function setFont(string $fontPathName): self
    {
        $this->fontPath = $fontPathName;
        return $this;
    }

    /**
     * @API
     * @param float $fontSize font size in px
     * @return $this
     */
    public function setFontSize(float $fontSize): self
    {
        $this->fontSize = SizeConversion::pxToPt($fontSize);
        return $this;
    }

    /**
     * @API
     * @param array $lineBreakCharacters
     * @return $this
     */
    public function setLinebreakCharacters(array $lineBreakCharacters): self
    {
        $this->linebreakCharacters = $lineBreakCharacters;
        return $this;
    }

    /**
     * @API
     * @param string $string
     * @return int
     */
    public function getStringWidth(string $string): int
    {
        $imageFtbBox = imageftbbox($this->fontSize, 0, $this->fontPath, $string);
        return $imageFtbBox === false ? 0 : $imageFtbBox[2];
    }

    /**
     * @API
     * @param string $string
     * @param int $boundingBoxWidth
     * @param bool $stripTags
     * @return int
     */
    public function getNrOfRows(string $string, int $boundingBoxWidth, bool $stripTags = true): int
    {
        $lines = [];
        if (strlen($string) > 0) {
            $widthOfASingleSpace = $this->getStringWidth(' ');

            // even out different line breaks
            if (!empty($this->linebreakCharacters)) {
                $replaceWith = $this->linebreakCharacters[key($this->linebreakCharacters)];
                $string      = str_replace($this->linebreakCharacters, $replaceWith, $string);
                // explode string on linebreaks
                $linesToTestForPossibleWraps = explode($replaceWith, $string);
            } else {
                $linesToTestForPossibleWraps = [$string];
            }

            // Break string on words
            foreach ($linesToTestForPossibleWraps as $line => $lineText) {
                if ($stripTags === true) {
                    $words = explode(' ', strip_tags($lineText));
                } else {
                    $words = explode(' ', $lineText);
                }
                $linesToTestForPossibleWraps[$line] = [];
                foreach ($words as $word) {
                    $wordWidth = $this->getStringWidth($word);
                    if ($wordWidth > $boundingBoxWidth) {
                        $splitOverflowingWord = $this->splitOverflowingWord($word, $boundingBoxWidth);
                        foreach ($splitOverflowingWord as $wordParts) {
                            $linesToTestForPossibleWraps[$line][] = $wordParts;
                        }
                    } else {
                        $linesToTestForPossibleWraps[$line][] = [
                            'string' => $word,
                            'width'  => $wordWidth
                        ];
                    }
                }
            }

            // get number of lines
            $currentLine = 0;
            foreach ($linesToTestForPossibleWraps as $line) { // for each line
                $lines[$currentLine] = new Line('', 0, $widthOfASingleSpace);
                foreach ($line as $word) { // for each word
                    $currentLineWidth = $lines[$currentLine]->getWidth();
                    if ($currentLineWidth === 0 || $currentLineWidth + $widthOfASingleSpace + $word['width'] <= $boundingBoxWidth) {
                        $lines[$currentLine]->addWord($word['string'], $word['width']);
                    } else {
                        // word doesn't fit on one line anymore, recalculate the string length including spaces and try again
                        $lines[$currentLine]->setWidth($this->getStringWidth($lines[$currentLine]->getLine()));
                        if ($lines[$currentLine]->getWidth() + $widthOfASingleSpace + $word['width'] <= $boundingBoxWidth) {
                            // word fits after recalculation
                            $lines[$currentLine]->addWord($word['string'], $word['width']);
                        } else {
                            // word didn't fit after all, create new line
                            $currentLine++;
                            $lines[$currentLine] = new Line($word['string'], $word['width'], $widthOfASingleSpace);
                        }
                    }
                }
                $currentLine++;
            }
        }
        return count($lines);
    }

    private function splitOverflowingWord(string $word, int $boundingBoxWidth): array
    {
        $wordCharacters          = str_split($word);
        $currentPart             = 0;
        $wordParts[$currentPart] = ['string' => '', 'width' => 0];
        while (!empty($wordCharacters)) {
            $nextCharacter = array_shift($wordCharacters);
            $combinedWidth = $this->getStringWidth($wordParts[$currentPart]['string'].$nextCharacter);
            if ($combinedWidth <= $boundingBoxWidth) {
                $wordParts[$currentPart]['string'] .= $nextCharacter;
                $wordParts[$currentPart]['width']  = $combinedWidth;
            } else {
                $currentPart++;
                $wordParts[$currentPart]['string'] = $nextCharacter;
                $wordParts[$currentPart]['width']  = $this->getStringWidth($nextCharacter);
            }
        }
        return $wordParts;
    }
}
