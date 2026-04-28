<?php

namespace App\Enum;

enum TicketType: string {
    case INCIDENT = 'incident';
    case REQUEST = 'request';
    case ONBOARDING = 'onboarding';
    case OFFBOARDING = 'offboarding';
}