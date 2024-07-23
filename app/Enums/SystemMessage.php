<?php

namespace App\Enums;

use App\Traits\EnumTools;

enum SystemMessage: int
{
    use EnumTools;

    case FAIL = 0;
    case SUCCESS = 1;
    case INTERNAL_ERROR = 10;
    case DATA_NOT_FOUND = 11;
    case PAGE_NOT_FOUND = 12;
    case BAD_DATA = 13;

    // Domain exception codes. starting from 100
    case DATA_EXIST = 100;
    case USER_NOT_FOUND = 101;

    /*case RECURSIVE_TRANSACTION = 100;
    case CURRENCY_MISMATCH = 101;
    case SOURCE_UNAVAILABLE = 102;
    case DESTINATION_UNAVAILABLE = 103;
    case INSUFFICIENT_BALANCE = 104;
    case MAX_BALANCE = 105;*/

}
