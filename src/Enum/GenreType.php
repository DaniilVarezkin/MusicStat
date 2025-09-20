<?php

namespace App\Enum;
enum GenreType: string
{
    case ROCK = 'rock';
    case POP = 'pop';
    case HIP_HOP = 'hip_hop';
    case ELECTRONIC = 'electronic';
    case JAZZ = 'jazz';
    case CLASSICAL = 'classical';
    case COUNTRY = 'country';
    case RNB = 'rnb';
    case REGGAE = 'reggae';
    case METAL = 'metal';
    case BLUES = 'blues';
    case FOLK = 'folk';
    case SOUL = 'soul';
    case FUNK = 'funk';
    case PUNK = 'punk';
    case ALTERNATIVE = 'alternative';
    case GLAM_ROCK = 'glam_rock';



    public function label(): string
    {
        return match($this) {
            self::ROCK => 'Рок',
            self::POP => 'Поп',
            self::HIP_HOP => 'Хип-хоп',
            self::ELECTRONIC => 'Электронная музыка',
            self::JAZZ => 'Джаз',
            self::CLASSICAL => 'Классическая музыка',
            self::COUNTRY => 'Кантри',
            self::RNB => 'R&B',
            self::REGGAE => 'Регги',
            self::METAL => 'Метал',
            self::BLUES => 'Блюз',
            self::FOLK => 'Фолк',
            self::SOUL => 'Соул',
            self::FUNK => 'Фанк',
            self::PUNK => 'Панк',
            self::ALTERNATIVE => 'Альтернатива',
            self::GLAM_ROCK => 'Глэм рок',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function choices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[] = $case->label();
        }
        return array_flip($choices);
    }
}
