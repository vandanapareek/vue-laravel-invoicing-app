<?php

namespace App\Enums;

enum PaymentTerms: int
{
    case OneDay = 1;
    case SevenDays = 7;
    case FourteenDays = 14;
    case ThirtyDays= 30;
} 