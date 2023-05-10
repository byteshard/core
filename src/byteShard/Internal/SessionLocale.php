<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\Locale;

class SessionLocale
{
    private string $locale                      = '';
    private array  $supportedApplicationLocales = [];

    public function __construct(string $locale) {
        $this->setUserSelectedLocale($locale);
    }

    public function setUserSelectedLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function setSupportedApplicationLocales(array $locales): void
    {
        $this->supportedApplicationLocales = $locales;
    }

    public function isSupportedLocale(string $locale): bool
    {
        return array_key_exists($locale, $this->supportedApplicationLocales);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getInterfaceLocale(): array
    {
        // create the locale for the javascript client
        $locales = [];
        foreach ($this->supportedApplicationLocales as $locale) {
            $locales[$locale] = Locale::getLocaleName($locale);
        }
        asort($locales);
        foreach ($locales as $locale => $text) {
            if ($this->locale === $locale) {
                $result['locales']['options'][] = array('value' => $locale, 'text' => $text, 'selected' => true);
            } else {
                $result['locales']['options'][] = array('value' => $locale, 'text' => $text);
            }
        }
        if (empty($locales) || count($locales) === 1) {
            $result['type'] = 'none';
        } else {
            $result['type'] = 'list';
        }
        return $result;
    }

}