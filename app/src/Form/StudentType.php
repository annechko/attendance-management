<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Institution;
use App\Entity\Intake;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Student $student */
        $student = $builder->getData();
        $course = $student?->getIntake()?->getCourse();
        $institution = $course?->getInstitution();
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
                'placeholder' => '',
                'data' => $institution,
                'placeholder_attr' => [
                    'data-role' => 'empty-value',
                ],
                'attr' => [
                    'data-role' => 'institution-select',
                ],
                'class' => Institution::class,
                'choice_label' => 'name',
                'choice_attr' => function (Institution $institution) {
                    return [
                        'data-institution-id' => $institution->getId(),
                    ];
                },
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.id', 'ASC');
                },
            ])
            ->add('course', EntityType::class, [
                'mapped' => false,
                'data' => $course,
                'placeholder' => '',
                'placeholder_attr' => [
                    'data-role' => 'empty-value',
                ],
                'attr' => [
                    'data-role' => 'course-select',
                ],
                'class' => Course::class,
                'choice_label' => 'name',
                'choice_attr' => function (Course $course) {
                    return [
                        'data-institution-id' => $course->getInstitution()->getId(),
                        'data-course-id' => $course->getId(),
                    ];
                },
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.id', 'ASC');
                },
            ])
            ->add('intake', EntityType::class, [
                'class' => Intake::class,
                'placeholder' => '',
                'placeholder_attr' => [
                    'data-role' => 'empty-value',
                ],
                'attr' => [
                    'data-role' => 'intake-select',
                ],
                'choice_label' => 'name',
                'choice_attr' => function (Intake $intake) {
                    return [
                        'data-course-id' => $intake->getCourse()->getId(),
                    ];
                },
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.id', 'ASC');
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
