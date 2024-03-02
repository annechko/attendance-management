<?php

namespace App\Form;

use App\Entity\Attendance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $statusConfig = [
            'label' => false,
            'choices' => [
                'Present' => Attendance::STATUS_PRESENT,
                'Absent' => Attendance::STATUS_ABSENT,
                'Excused' => Attendance::STATUS_EXCUSED,
            ],
        ];
        $entityId = $builder->getData()?->getId();
        if ($entityId === null) {
            $statusConfig['placeholder'] = '';

            $statusConfig['placeholder_attr'] = [
                'data-role' => 'empty-value',
            ];
        } else {
            $statusConfig['attr'] = [
                'data-chosen' => $builder->getData()->getStatus(),
            ];
        }

        $commentConfig = [
            'label' => false,
            'attr' => [
                'class' => ' js-attendance-comment attendance-comment form-control form-control-sm',
            ],
        ];
        if ($entityId !== null) {
            if ($builder->getData()->getStatus() === Attendance::STATUS_PRESENT) {
                $commentConfig['attr']['class'] .= ' hide';
            }
        } else {
            $commentConfig['attr']['class'] .= ' hide';
        }
        $builder
            ->add('status', ChoiceType::class, $statusConfig)
            ->add('comment', TextareaType::class, $commentConfig)
            ->add('studentName', HiddenType::class, [
                'disabled' => true,
                'mapped' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('studentEmail', HiddenType::class, [
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
