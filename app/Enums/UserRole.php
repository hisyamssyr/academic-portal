<?php

namespace App\Enums;

enum UserRole: string
{
    case Mahasiswa = 'mahasiswa';
    case Asdos = 'asdos';
    case Dosen = 'dosen';

    public function label(): string
    {
        return match ($this) {
            self::Mahasiswa => 'Mahasiswa',
            self::Asdos => 'Asisten Dosen',
            self::Dosen => 'Dosen',
        };
    }

    public function isStaff(): bool
    {
        return in_array($this, [self::Asdos, self::Dosen], true);
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role): string => $role->value, self::cases());
    }

    /**
     * @return array<string>
     */
    public static function staffValues(): array
    {
        return array_values(array_map(
            fn (self $role): string => $role->value,
            array_filter(self::cases(), fn (self $role): bool => $role->isStaff()),
        ));
    }
}
