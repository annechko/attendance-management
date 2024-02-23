<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Institution;
use App\Entity\Intake;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('name')
            ->add('surname')
            ->add('dateOfBirth')
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                    //'Other' => 'Other',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('institution', EntityType::class, [
                'mapped' => false,
                'class' => Institution::class,
                'choice_label' => 'name',
            ])
            ->add('course', EntityType::class, [
                'mapped' => false,
                'class' => Course::class,
                'choice_label' => 'name',
                'group_by' => function (Course $choice, $key, $value) {
                    $institution = $choice->getInstitution();
                    return $institution->getName() . ' (ID=' . $institution->getId() . ')';
                },
            ])
            ->add('intake', EntityType::class, [
                'class' => Intake::class,
                'choice_label' => 'name',
                'group_by' => function (Intake $choice, $key, $value) {
                    $course = $choice->getCourse();
                    return $course->getName() . ' (ID=' . $course->getId() . ')';
                },
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
