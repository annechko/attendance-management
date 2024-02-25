<?php

namespace App\Form;

use App\Entity\Attendance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    '' => null,
                    'Present' => Attendance::STATUS_PRESENT,
                    'Absent' => Attendance::STATUS_ABSENT,
                    'Excused' => Attendance::STATUS_EXCUSED,
                ],
            ])
            ->add('studentName', HiddenType::class, [
                'disabled' => true,
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('dateValue', HiddenType::class)
            ->add('studentId', HiddenType::class, [
                'setter' => function () {
                },
            ])
            ->add('teacherId', HiddenType::class, [
                'setter' => function () {
                },
            ])
            ->add('subjectId', HiddenType::class, [
                'setter' => function () {
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attendance::class,
            'attr' => [
                'class' => 'd-flex',
            ],
        ]);
    }
}
