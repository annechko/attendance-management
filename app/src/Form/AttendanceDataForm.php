<?php

namespace App\Form;

use App\Entity\Intake;
use App\Entity\Subject;
use App\Repository\IntakeRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendanceDataForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intake', EntityType::class, [
                'placeholder' => '',
                'class' => Intake::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select mx-1',
                    'style' => 'min-width: 250px;',
                ],
                'query_builder' => function (IntakeRepository $er) use ($options): QueryBuilder {
                    $intakeIds = $options['intakeIds'];
                    if (count($intakeIds) === 0) {
                        return $er->createQueryBuilder('i');
                    }
                    $b = $er->createQueryBuilder('i');
                    return $b
                        ->andWhere($b->expr()->in('i.id', $intakeIds));
                },
            ])
            ->add('subject', EntityType::class, [
                'placeholder' => '',
                'class' => Subject::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select mx-1',
                    'style' => 'min-width: 250px;',
                ],
                'query_builder' => function (SubjectRepository $er) use ($options): QueryBuilder {
                    $subjectIds = $options['subjectIds'];
                    if (count($subjectIds) === 0) {
                        return $er->createQueryBuilder('i');

                    }
                    $b = $er->createQueryBuilder('s');
                    return $b
                        ->andWhere($b->expr()->in('s.id', $subjectIds));
                },
            ])
            ->add('date', DateType::class, [
                'data' => new \DateTimeImmutable(),
                'attr' => [
                    'class' => 'mx-1',
                    'style' => 'min-width: 250px;',
                ],
            ])
            ->add('Filter', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-secondary btn-sm mt-4 mx-2',
                    'name' => null,
                ],
            ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'intakeIds' => [],
            'subjectIds' => [],
            'data_class' => AttendanceData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'attr' => [
                'class' => 'd-flex w-100 mb-4',
            ],
        ]);
    }
}