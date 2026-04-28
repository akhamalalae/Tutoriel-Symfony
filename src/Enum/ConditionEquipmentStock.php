<?php

namespace App\Enum;

enum ConditionEquipmentStock: string
{
    case NEW = 'new';
    case GOOD = 'good';
    case FAIR = 'fair';
    case BROKEN = 'broken';
}