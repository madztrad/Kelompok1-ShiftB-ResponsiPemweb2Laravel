<?php

namespace App\Enums;

enum ClaimStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}