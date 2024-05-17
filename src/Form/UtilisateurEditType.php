<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UtilisateurEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            
            
            ->add('name');
            if ( $options['user_role'] == 'Freelancer' ) {
                $builder
            ->add('LastName');
            }
            $builder
            ->add('UserName')
            ->add('email')
            
            ->add('ImagePath', FileType::class, [
                'label' => 'Image (JPG file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        
                        'mimeTypesMessage' => 'Please upload a valid JPG document',
                    ])
                ],
            ]) ;
            if ( $options['user_role'] == 'Freelancer' ) {
                $builder
                ->add('bio')
                ->add('experience')
                ->add('education');
               }
               if ( $options['user_role'] == 'Entreprise' ) {
    
                $builder
                ->add('domaine')
                ->add('info')
                ->add('location')
                ->add('nbe');
               }
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'user_role' => null,
        ]);
    }
}
