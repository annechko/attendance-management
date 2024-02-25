<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Intake;
use App\Entity\Subject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendanceDataForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('course', EntityType::class, [
                'placeholder' => '',
                'class' => Course::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select',
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('intake', EntityType::class, [
                'placeholder' => '',
                'class' => Intake::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select',
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('subject', EntityType::class, [
                'placeholder' => '',
                'class' => Subject::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select',
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('date', DateType::class, [
                'data' => new \DateTimeImmutable(),
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttendanceData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'attr' => [
                'class' => 'd-flex w-100 mb-4',
            ],
        ]);
    }
}