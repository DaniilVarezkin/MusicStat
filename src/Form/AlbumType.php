<?php

namespace App\Form;

use App\Dto\CreateAlbumDto;
use App\Entity\Artist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Название',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('criticScore', IntegerType::class, [
                'label' => 'Оценка администрации',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('releaseDate', DateType::class, [
                'label' => 'Дата релиза',
                'widget' => 'single_text', // красивый Bootstrap date input
                'attr' => ['class' => 'form-control'],
            ])
            ->add('authors', EntityType::class, [
                'class' => Artist::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Исполнители',
                'attr' => [
                    'class' => 'select2 form-select',
                ],
            ])
            ->add('cover', FileType::class, [
                'required' => false,
                'label' => 'Обложка',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateAlbumDto::class,
        ]);
    }
}
