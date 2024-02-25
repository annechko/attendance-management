<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Intake;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class IntakeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'choice_label' => 'name',
            ])
            ->add('name')
            ->add('start')
            ->add('finish')
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $intake = $event->getData();
                $form = $event->getForm();
                if ($intake->getCourse() !== null) {
                    $form->add('course', EntityType::class, [
                        'class' => Course::class,
                        'choice_label' => 'name',
                        'disabled' => true, // Set disabled attribute
                        'placeholder' => 'Select an Intake', // Placeholder text
                        'required' => false,
                    ]);
                }
            });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intake::class,
        ]);
    }
}
