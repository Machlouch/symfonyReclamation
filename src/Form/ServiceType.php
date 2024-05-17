<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'The name must be at least {{ limit }} characters long',
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a description',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'The description must be at least {{ limit }} characters long',
                    ]),
                ],
            ])
            ->add('duree', DateTimeType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a duration',
                    ]),
                ],
            ])
            ->add('disponibilite', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter availability',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'The availability must be at least {{ limit }} characters long',
                    ]),
                ],
            ])
            ->add('prix')
            ->add('date_de_creation', DateTimeType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a creation date',
                    ]),
                ],
            ])
            ->add('id_user', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a user ID',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
        