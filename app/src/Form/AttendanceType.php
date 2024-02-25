<?php

namespace App\Form;

use App\Entity\Attendance;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('status')
            ->add('comment')
            ->add('student', EntityType::class, [
                'class' => Student::class,
'choice_label' => 'id',
            ])
            ->add('teacher', EntityType::class, [
                'class' => Teacher::class,
'choice_label' => 'id',
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attendance::class,
        ]);
    }
}
