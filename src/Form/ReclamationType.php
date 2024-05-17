<?php

namespace App\Form;


use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Correct import for TextType
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Ensure TextareaType is imported
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Sujet', TextType::class, [
                'label' => 'Sujet',
                'attr' => ['class' => 'form-control', 'id' => 'sujet']
            ])
            ->add('Description', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['class' => 'form-control', 'id' => 'description', 'cols' => 30, 'rows' => 7, 'placeholder' => 'Write your notes or questions here...']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
