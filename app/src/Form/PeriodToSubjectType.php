<?php

namespace App\Form;

use App\Entity\Period;
use App\Entity\PeriodToSubject;
use App\Entity\Subject;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Required;

class PeriodToSubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityId = $builder->getData()?->getId();
        $builder
            ->add('totalNumberOfLessons')
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'attr' => [
                    'required' => 'required',
                ],
                'disabled' => $entityId > 0, // not editable after creation.
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($options): QueryBuilder {
                    $courseId = $options['courseId'];
                    if ($courseId > 0) {
                        return $er->createQueryBuilder('s')
                            ->where('s.course = :course')
                            ->setParameter(':course', $courseId);
                    }
                    return $er->createQueryBuilder('s');
                },
                'constraints' => [new Required()],
            ])
            ->add('period', EntityType::class, [
                'class' => Period::class,
                'disabled' => $entityId > 0, // not editable after creation.
                'choice_label' => 'name',
                'attr' => [
                    'required' => 'required',
                ],
                'query_builder' => function (EntityRepository $er) use ($options): QueryBuilder {
                    $periodId = $options['periodId'];
                    if ($periodId > 0) {
                        return $er->createQueryBuilder('p')
                            ->where('p.id = :id')
                            ->setParameter(':id', $periodId);
                    }
                    return $er->createQueryBuilder('p');
                },
                'constraints' => [new Required()],
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PeriodToSubject::class,
            'periodId' => 0,
            'courseId' => 0,
        ]);
    }
}
