<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('Text', TextareaType::class)
            ->add('Accessibility', ChoiceType::class, ['choices'=>['PUBLIC' =>'PUBLIC', 'COMPANY'=>'COMPANY', 'PRIVATE'=>'PRIVATE']])
            ->add('Status', ChoiceType::class, ['choices' => ['Unfinished' =>'False', "Finished" => 'True']])
            ->add('Deadline', DateTimeType::class, ['date_widget' => 'single_text', 'time_widget'=>'single_text'])
        ;
    }
    // , ['widget' => 'single_text']
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            
        ]);
    }
}
