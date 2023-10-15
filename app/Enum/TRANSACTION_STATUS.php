<?php

namespace App\Enum;

enum TRANSACTION_STATUS: string
{
    case PAID = 'paid';
    case OUTSTANDING = 'outstanding';
    case OVERDUE = 'overdue';
    case VOID = 'void';
}