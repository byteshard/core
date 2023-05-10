<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Locale;

use byteShard\Locale;

/**
 * Class de
 * @package byteShard\Internal\Locale
 *
 * contains all texts for the framework in german (generic)
 * Don't use the same token over and over again.
 * Make specific tokens for every single message and rather refer to the default message
 * That way in debug mode the token can be returned and is easy to find
 *
 * prepend an array dimension named "debug" for a message that will be displayed in the browser while debug is true
 * e.g.:
 * self::$locale['test'] = 'something happened';
 * self::$locale['debug']['test'] = "the coder didn't specify xyz";
 */
class de extends Locale
{
    private string $default_message = 'Ein unbekannter Fehler ist aufgetreten';

    protected function get_basecontainer_locale(): void
    {
        self::$locale['button']['logout'] = 'abmelden';
    }

    protected function get_form_locale(): void
    {
        self::$locale['cell']['Label']['noPermission']['Label'] = 'Sie haben keine Berechtigung diese Daten einzusehen';
    }

    protected function get_database_locale(): void
    {
        self::$locale['update']['generic']              = 'Fehler beim aktualisieren';
        self::$locale['update']['permission']           = 'Sie haben keine Berechtigung diese Aktion auszuführen';
        self::$locale['update']['query']                = 'Query konnte nicht erstellt werden';
        self::$locale['update']['failed']               = 'Die Daten konnten nicht aktualisert werden';
        self::$locale['update']['norecord']             = 'Es wurde kein Eintrag zum aktualisieren gefunden';
        self::$locale['update']['evaluate_data_failed'] = 'Es wurden keine Daten zum aktualisieren gefunden';
        self::$locale['update']['connection_failed']    = 'Es konnte keine Verbindung zur Datenbank hergestellt werden';
    }

    protected function get_action_locale(): void
    {
        self::$locale['generic'] = $this->default_message;
    }

    protected function get_upload_locale(): void
    {
        self::$locale['sanitizer']['filename']['invalidCharacters'] = 'Der Dateiname enthält nicht zugelassene Zeichen';
        self::$locale['sanitizer']['filename']['length']            = 'Der Dateiname ist zu lang';
        self::$locale['sanitizer']['fileType']['invalid']           = 'Dieser Dateityp ist nicht zugelassen';
        self::$locale['file']['size']['ini']                        = 'Die ausgewählte Datei ist zu groß';
        self::$locale['file']['size']['form']                       = 'Die ausgewählte Datei ist zu groß';
        self::$locale['file']['partial']                            = 'Die ausgewählte Datei konnte nicht vollständig hochgeladen werden';
        self::$locale['file']['notUploaded']                        = 'Die ausgewählte Datei konnte nicht hochgeladen werden';
    }

    protected function get_cellContent_locale(): void
    {
        self::$locale['generic']                                = $this->default_message;
        self::$locale['no_failed_validation_messages']          = 'Bitte überprüfen Sie Ihre Eingabe';
        self::$locale['debug']['no_failed_validation_messages'] = 'Mindestens eine ValidationFailed, aber keine Fehlermeldung';
        self::$locale['unexpected_client_data']                 = $this->default_message;
        self::$locale['debug']['unexpected_client_data']        = 'Der übergebene Parameter ist weder vom Typ Struct\ClientData noch Struct\ValidationFailed';
        self::$locale['permission']                             = 'Sie haben keine Berechtigung diese Daten zu modifizieren';
        self::$locale['undefined_method']                       = $this->default_message;
    }

    protected function get_grid_locale(): void
    {
        self::$locale['generic']                                = $this->default_message;
        self::$locale['debug']['method_defineUpdate_not_found'] = 'Die Methode defineUpdate wurde nicht definiert';
    }

    protected function get_eventHandler_locale(): void
    {
        self::$locale['byteShard']['notfound']            = $this->default_message;
        self::$locale['byteShard']['tab']['notfound']     = 'Interner Fehler: unbekannter Tab';
        self::$locale['id']['invalid']                    = 'Interner Fehler: ungültiger Schlüssel';
        self::$locale['gridEdit']['token']['invalid']     = $this->default_message;
        self::$locale['gridEdit']['token']['notFound']    = $this->default_message;
        self::$locale['gridEdit']['generic']              = $this->default_message;
        self::$locale['gridEdit']['class']['wrongParent'] = $this->default_message;
        self::$locale['gridEdit']['class']['notFound']    = $this->default_message;
    }

    protected function get_errorHandler_locale(): void
    {
        self::$locale['popup_title'] = 'Fehler';
    }

    protected function get_validate_locale(): void
    {
        self::$locale['form']['field']                           = "Fehleingabe im Feld '%s': %s";
        self::$locale['grid']['column']                          = "Fehleingabe in der Spalte '%s': %s";
        self::$locale['rule']['min_length']                      = 'mindestens %s Zeichen erwartet';
        self::$locale['rule']['max_length']                      = 'höchstens %s Zeichen erwartet';
        self::$locale['rule']['enum']                            = 'enum erwartet';
        self::$locale['rule']['valid_email']                     = 'gültige email erwartet';
        self::$locale['type']['string']                          = 'string erwartet';
        self::$locale['type']['int']                             = 'Zahl erwartet';
        self::$locale['type']['tinyint']                         = 'Zahl erwartet';
        self::$locale['type']['bit']                             = 'boolean erwartet';
        self::$locale['type']['bigint_date']                     = 'Datum erwartet';
        self::$locale['type']['date']                            = 'Datum erwartet';
        self::$locale['type']['datetime']                        = 'Datum/Uhrzeit erwartet';
        self::$locale['type']['datetime_create_failed']          = 'Datum erwartet';
        self::$locale['debug']['type']['datetime_create_failed'] = 'Couldn\'t create DateTime::createFromFormat';
        self::$locale['type']['id']                              = 'id erwartet';
    }

    protected function get_popup_locale(): void
    {
        self::$locale['message']['error']                  = 'Fehler';
        self::$locale['message']['warning']                = 'Warnung';
        self::$locale['message']['notice']                 = 'Hinweis';
        self::$locale['message']['button']['ok']           = 'Ok';
        self::$locale['confirmation']['button']['proceed'] = 'Ok';
        self::$locale['confirmation']['button']['cancel']  = 'Abbrechen';
    }

    protected function get_data_locale(): void
    {
        self::$locale['insert']['failed']['label'] = 'Fehler beim Anlegen';
        self::$locale['update']['failed']['label'] = 'Fehler beim Aktualisieren';
        //Data\Archive class
        self::$locale['archive']['failed']['label'] = 'Fehler beim Archivieren';
    }

    protected function get_bs_export_locale(): void
    {
        self::$locale['timeout'] = 'Zeitüberschreitung der Anfrage';
        self::$locale['error']   = 'Ein Fehler ist aufgetreten';
    }

    protected function get_date_locale(): void
    {
        /**
         * .client is the format that will be displayed in the client to the user (dhx formatting)
         * .server is the format that will be returned to the server (dhx formatting)
         * .object is the format that will be used by php DateTime::createFromFormat
         */
        // deprecated, remove once not used anymore
        self::$locale['grid']['date_time']['client'] = 'd.m.Y H:i:s';
        self::$locale['grid']['date_time']['server'] = 'd.m.Y H:i:s';
        self::$locale['grid']['date_time']['object'] = 'd.m.Y H:i:s';
        self::$locale['grid']['date']['client']      = 'd.m.Y';
        self::$locale['grid']['date']['server']      = 'd.m.Y';
        self::$locale['grid']['date']['object']      = 'd.m.Y|';
        self::$locale['grid']['time']['client']      = 'H:i:s';
        self::$locale['grid']['time']['server']      = 'H:i:s';
        self::$locale['grid']['time']['object']      = 'H:i:s';
        self::$locale['form']['date_time']['client'] = '%d.%m.%Y %H:%i:%s';
        self::$locale['form']['date_time']['server'] = '%Y-%m-%d %H:%i:%s';
        self::$locale['form']['date_time']['object'] = 'Y-m-d H:i:s';
        self::$locale['form']['date']['client']      = '%d.%m.%Y';
        self::$locale['form']['date']['server']      = '%Y-%m-%d';
        self::$locale['form']['date']['object']      = 'Y-m-d|';
        self::$locale['form']['time']['client']      = '%H:%i:%s';
        self::$locale['form']['time']['server']      = '%H:%i:%s';
        self::$locale['form']['time']['object']      = 'H:i:s';

        self::$locale['dateTime']['grid'] = 'd.m.Y H:i:s';
        self::$locale['date']['grid']     = 'd.m.Y';
        self::$locale['time']['grid']     = 'H:i:s';

        self::$locale['month'][1]  = 'Januar';
        self::$locale['month'][2]  = 'Februar';
        self::$locale['month'][3]  = 'März';
        self::$locale['month'][4]  = 'April';
        self::$locale['month'][5]  = 'Mai';
        self::$locale['month'][6]  = 'Juni';
        self::$locale['month'][7]  = 'Juli';
        self::$locale['month'][8]  = 'August';
        self::$locale['month'][9]  = 'September';
        self::$locale['month'][10] = 'Oktober';
        self::$locale['month'][11] = 'November';
        self::$locale['month'][12] = 'Dezember';
        self::$locale['day'][1]    = 'Montag';
        self::$locale['day'][2]    = 'Dienstag';
        self::$locale['day'][3]    = 'Mittwoch';
        self::$locale['day'][4]    = 'Donnerstag';
        self::$locale['day'][5]    = 'Freitag';
        self::$locale['day'][6]    = 'Samstag';
        self::$locale['day'][7]    = 'Sonntag';
    }
}
