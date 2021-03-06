<?php

namespace AppBundle\Form\Todo;

use AppBundle\Entity\DistributionList;
use AppBundle\Entity\TodoStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Todo;
use AppBundle\Entity\User;
use AppBundle\Entity\TodoCategory;

class BaseCreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
            ])
            ->add('responsibility', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'choice_label' => 'username',
                'placeholder' => 'placeholder.user',
                'translation_domain' => 'messages',
            ])
            ->add('dueDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
            ])
            ->add('status', EntityType::class, [
                'class' => TodoStatus::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'placeholder.status',
                'translation_domain' => 'messages',
                'choice_translation_domain' => 'messages',
            ])
            ->add('todoCategory', EntityType::class, [
                'class' => TodoCategory::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'placeholder.todo_category',
                'translation_domain' => 'messages',
            ])
            ->add('distributionList', EntityType::class, [
                'class' => DistributionList::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'placeholder.distribution_list',
                'translation_domain' => 'messages',
            ])
            ->add('showInStatusReport', CheckboxType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Todo::class,
            'allow_extra_fields' => true,
        ]);
    }
}
