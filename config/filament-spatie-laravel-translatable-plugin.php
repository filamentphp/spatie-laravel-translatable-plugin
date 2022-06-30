<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Locales
    |--------------------------------------------------------------------------
    |
    | These are the locales that Filament will use to put translate resource
    | content. They may be overridden for each resource by setting the
    | `$translatableLocales` property.
    |
    */

    'default_locales' => [config('app.locale')],


    /*
    |--------------------------------------------------------------------------
    | Display Locale Switcher
    |--------------------------------------------------------------------------
    |
    | In case you are using a self-made locale switcher or
    | a other package like https://filamentphp.com/plugins/language-switch
    | you can disable the default shipped one here.
    |
    */
    'display_locale_switcher' => true,

];
