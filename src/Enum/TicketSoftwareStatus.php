<?php

namespace App\Enum;

enum TicketSoftwareStatus: string
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case DELIVERED = 'delivered';
    case INSTALLED = 'installed';
}