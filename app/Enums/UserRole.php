<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case AGENT = 'agent';
    case CHARGE = 'charge';
    case SUPPORT = 'support';
    case MIS = 'mis';
    case CHANGES = 'changes';
    case MIS_MANAGER = 'mis-manager';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::MANAGER => 'Manager',
            self::AGENT => 'Agent',
            self::CHARGE => 'Charge Team',
            self::SUPPORT => 'Support Team',
            self::MIS => 'MIS Team',
            self::CHANGES => 'Changes Team',
            self::MIS_MANAGER => 'MIS Manager',
        };
    }
    
    public function prefix(): string
    {
        return match($this) {
            self::ADMIN => 'ADM',
            self::MANAGER => 'MGR',
            self::AGENT => 'AGT',
            self::CHARGE => 'CHG',
            self::SUPPORT => 'SUP',
            self::MIS => 'MIS',
            self::MIS_MANAGER => 'MIS_MGR',
            self::CHANGES => 'CHNG',
        };
    }
}