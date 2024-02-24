<?php

namespace App\Form;

use App\Filter\CourseFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseFilterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Name',
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('duration', Type\TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Location',
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('id', Type\TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Id',
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('institution', Type\TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Institution',
                    'onchange' => 'this.form.submit()',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourseFilter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
