<?php

namespace App\Form;

use App\Entity\Festival;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FestivalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('start_date', DateType::class, [
                'constraints' => [
                    new Callback(function ($date, ExecutionContextInterface $context) {
                        if ($date <= new \DateTime('today')) {
                            $context->buildViolation('The start date must be after today')
                                ->addViolation();
                        }
                    })
                ]
            ])
            ->add('end_date', DateType::class, [
                'constraints' => [
                    new Callback(function ($date, ExecutionContextInterface $context) {
                        if ($date <= new \DateTime('tomorrow')) {
                            $context->buildViolation('The end date must be after tomorrow');
                        }
                    })
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Festival Poster',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2048k',
                        extensions: ['png', 'jpg', 'jpeg'],
                        extensionsMessage: 'Please upload a valid image file.'
                    )
                ]
            ])
            ->add('location')
            ->add('price')
//            ->add('bands', EntityType::class, [
//                    'class' => Band::class,
//                    'choice_label' => 'id',
//                    'multiple' => true,
//                ]
//            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Festival::class,
        ]);
    }
}
