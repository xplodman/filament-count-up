<?php

// config for Xplodman/CountUp
return [

    /*
     * Default number of decimal places shown, when a component/column
     * does not explicitly call ->decimals().
     */
    'decimals' => 0,

    /*
     * Default animation duration in milliseconds.
     */
    'duration' => 1000,

    /*
     * Default thousands and decimal separators used to format the animated
     * number. These are intentionally plain ASCII characters (not locale
     * aware via Intl.NumberFormat) so the animated value always matches
     * whatever separators the rest of your app already uses.
     */
    'thousands_separator' => ',',

    'decimal_separator' => '.',

];
