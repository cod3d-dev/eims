<?php


namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
enum ImmigrationStatus: string implements HasLabel
{
    case Citizen = 'citizen';
    case Resident = 'resident';
    case AsylumSeeker = 'asylum_seeker';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Citizen => 'Ciudadano',
            self::Resident => 'Residente Permanente',
            self::AsylumSeeker => 'Aplicante de Asilo',
            self::Other => 'Otro a permiso de trabajo',
        };
    }

}
