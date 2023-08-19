<?php

namespace App\Enums;

enum TokenTypeEnum: int
{
    use EnumToArray;

    case EMAIL_VERIFICATION = 1;
    case PASSWORD_RESET = 2;
}
