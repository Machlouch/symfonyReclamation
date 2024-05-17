<?php

// src/Form/RatingType.php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


    class RatingType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                // ... other fields
                ->add('score', ChoiceType::class, [
                    'choices' => [
                        '1 star' => 1,
                        '2 stars' => 2,
                        '3 stars' => 3,
                        '4 stars' => 4,
                        '5 stars' => 5,
                    ],
                    'expanded' => true, // This will render them as radio buttons
                    'multiple' => false,
                    'label' => 'Score',
                    // Add a class to style with CSS
                    'attr' => ['class' => 'star-rating'],
                ])
                ->add('comment', TextareaType::class, [
                    'label' => 'Comment (optional)',
                    'required' => false,
                ]);
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
