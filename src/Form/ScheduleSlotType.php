<?php

namespace App\Form;

use App\Entity\Band;
use App\Entity\Festival;
use App\Entity\ScheduleSlot;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime', DateTimeType::class, [
                'label' => 'Start',
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'End',
            ])
            ->add('band', EntityType::class, [
                'class' => Band::class,
                'choice_label' => 'name',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('b')
                        ->orderBy('b.name', 'ASC');
                },
                'label' => 'Band'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('festival');
        $resolver->setAllowedTypes('festival', Festival::class);
        $resolver->setDefaults([
            'data_class' => ScheduleSlot::class,
        ]);
    }
}
