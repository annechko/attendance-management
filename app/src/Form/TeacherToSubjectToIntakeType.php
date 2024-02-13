<?php

namespace App\Form;

use App\Entity\Intake;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TeacherToSubjectToIntake;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Required;

class TeacherToSubjectToIntakeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityId = $builder->getData()?->getId();
        $teacherId = (int) $options['teacherId'];

        $builder
            ->add('teacher', EntityType::class, [
                'class' => Teacher::class,
                'choice_label' => 'fullNameWithEmail',
                'attr' => [
                    'required' => 'required',
                ],
                'disabled' => $entityId > 0 || $teacherId > 0,
                // when editing or when teacher specified.
                'query_builder' => function (EntityRepository $er) use ($teacherId): QueryBuilder {
                    if ($teacherId > 0) {
                        return $er->createQueryBuilder('t')
                            ->where('t.id = :id')
                            ->setParameter(':id', $teacherId);
                    }
                    return $er->createQueryBuilder('t');
                },
                'constraints' => [new Required()],
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'disabled' => $entityId > 0, // when editing.
                'constraints' => [new Required()],
                'group_by' => function (Subject $choice, $key, $value) {
                    $course = $choice->getCourse();
                    return $course->getName() . ' (ID=' . $course->getId() . ')';
                },
            ])
            ->add('intake', EntityType::class, [
                'class' => Intake::class,
                'choice_label' => 'name',
                'constraints' => [new Required()],
                'disabled' => $entityId > 0, // when editing.
                'group_by' => function (Intake $choice, $key, $value) {
                    $course = $choice->getCourse();
                    return $course->getName() . ' (ID=' . $course->getId() . ')';
                },
            ])
            ->add('start', null, [
                'help' => 'Since when this teacher is going to teach this subject',
            ])
            ->add('finish', null, [
                'help' => 'Till what date this teacher is going to teach this subject',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TeacherToSubjectToIntake::class,
            'teacherId' => 0,
        ]);
    }
}
