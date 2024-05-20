<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto Invoice Intervals
    |--------------------------------------------------------------------------
    |
    | * note that if u changed in key or value in this array
    | you must make sure to update the match expression 
    | in the calculateNextGenerateDate() function placed in App\Models\AutoInvoices.php file
    |
    */
    
    'custom_interval' => [
        1 => 'Monthly',
        2 => 'Quarterly',
        3 => 'Yearly',
    ],

];
