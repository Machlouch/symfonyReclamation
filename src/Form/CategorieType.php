<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
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
            ->add('description', null, [
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
            ->add('service_associe', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter associated services',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'The associated services must be at least {{ limit }} characters long',
                    ]),
                ],
            ])
            ->add('date_de_creation', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a creation date',
                    ]),
                ],
            ])
            ->add('imagePath', FileType::class, [
                'label' => 'Image (JPEG or PNG file)',
                
                // unmapped means that this field is not associated with any entity property
                'mapped' => false, 
                
                // make it optional so you don't have to re-upload the file
                // every time you edit the details
                'required' => false, 
                
                // do not include the file in the SQL INSERT statement
                // (use a lifecycle callback or event listener to handle file upload)
                'constraints' => [
                    new File([
                        'maxSize' => '5M', // maximum file size
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image.',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
