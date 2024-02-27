<?php

namespace App\Form;

use App\Entity\Intake;
use App\Entity\Period;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intake', EntityType::class, [
                'class' => Intake::class,
                'choice_label' => 'nameWithCourse',
            ])
            ->add('name')
            ->add('start')
            ->add('finish')
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $period = $event->getData();
                $form = $event->getForm();

                if ($period->getIntake() !== null) {
                    $form->add('intake', EntityType::class, [
                        'class' => Intake::class,
                        'choice_label' => 'nameWithCourse',
                        'disabled' => true, // Set disabled attribute
                        'required' => false, // Make the field not required
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Period::class,
        ]);
    }
}
