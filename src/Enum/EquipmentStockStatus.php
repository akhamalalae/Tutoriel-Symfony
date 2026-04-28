<?php

namespace App\Enum;

enum EquipmentStockStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case ASSIGNED = 'assigned';
    case IN_REPAIR = 'in_repair';
    case RETIRED = 'retired';
    case OUT_OF_SERVICE = 'out_of_service';
}