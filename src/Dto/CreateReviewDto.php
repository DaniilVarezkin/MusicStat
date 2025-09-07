<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateReviewDto
{
    public function __construct(

        #[Assert\NotBlank(message: 'Пожалуйста, укажите оценку')]
        #[Assert\Range(
            notInRangeMessage: 'Оценка должна быть от {{ min }} до {{ max }}',
            min: 1,
            max: 100
        )]
        public ?int    $score = null,

        #[Assert\Length(
            max: 1000,
            maxMessage: 'Отзыв не должен превышать {{ limit }} символов'
        )]
        public ?string $text = null
    )
    {
    }
}
