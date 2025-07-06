<?php

namespace App\Form;

use App\Entity\Band;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectBandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $festival = $options['festival'];

        $builder->add('band', EntityType::class, [
            'class' => Band::class,
            'choice_label' => 'name',
            'multiple' => false,
            'query_builder' => function (EntityRepository $er) use ($festival) {
                return $er->createQueryBuilder('b')
                    ->where(':festival NOT MEMBER OF b.festivals')
                    ->setParameter('festival', $festival);
            },
            'label' => 'Select a Band',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'festival' => null,
        ]);
    }
}

