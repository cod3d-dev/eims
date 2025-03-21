<?php


namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum DocumentStatus: string implements HasLabel, HasColor
{

    case Pending = 'pending';
    case Sent = 'sent';
    case Approved = 'approved';
    case Expired = 'expired';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Sent => 'Enviado',
            self::Approved => 'Aprobado',
            self::Expired => 'Expirado',
            self::Rejected => 'Rechazado',
        };
    }



    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'pending',
            self::Sent => 'info',
            self::Approved => 'success',
            self::Expired => 'warning',
            self::Rejected => 'danger',
        };
    }
}
