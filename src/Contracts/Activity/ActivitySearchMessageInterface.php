<?php

namespace App\Contracts\Activity;

use App\Entity\SearchMessage;

interface ActivitySearchMessageInterface
{
    public function decryptSearchMessage(SearchMessage $searchMessage): void;
    public function encryptSearchMessage(SearchMessage $searchMessage): void;
}