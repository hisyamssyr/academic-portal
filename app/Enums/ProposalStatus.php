<?php

namespace App\Enums;

enum ProposalStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Revised = 'revised';
    case Reviewed = 'reviewed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'DRAFT',
            self::Submitted => 'SUBMITTED',
            self::Revised => 'REVISION',
            self::Reviewed => 'REVIEWED',
        };
    }
}
