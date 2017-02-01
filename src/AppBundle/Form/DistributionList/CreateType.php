<?php

namespace AppBundle\Form\DistributionList;

use AppBundle\Entity\DistributionList;
use AppBundle\Entity\Meeting;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.constraints.general_field.name.not_blank',
                    ]),
                ],
            ])
            ->add('sequence', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.constraints.general_field.sequence.not_blank',
                    ]),
                    new Regex([
                        'pattern' => '/^([1-9]+\d*)$|^0$/',
                        'message' => 'validation.constraints.general_field.sequence.invalid',
                    ]),
                ],
            ])
            ->add('project', EntityType::class, [
                'required' => true,
                'class' => Project::class,
                'choice_label' => 'name',
                'placeholder' => 'admin.project.choice',
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.constraints.distribution_list.project.not_blank',
                    ]),
                ],
                'translation_domain' => 'admin',
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'translation_domain' => 'admin',
                'multiple' => true,
            ])
            ->add('meetings', EntityType::class, [
                'class' => Meeting::class,
                'choice_label' => 'name',
                'translation_domain' => 'admin',
                'multiple' => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DistributionList::class,
        ]);
    }
}