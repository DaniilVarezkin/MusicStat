<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\CreateReviewDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('score', IntegerType::class, [
                'label' => 'Оценка',
                'attr' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Текст отзыва',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateReviewDto::class,
        ]);
    }
}
