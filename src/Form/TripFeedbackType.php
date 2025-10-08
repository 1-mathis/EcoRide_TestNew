<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TripFeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $options): void
    {
        $b
            ->add('ok', ChoiceType::class, [
                'label' => 'Le trajet s’est-il bien passé ?',
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Note (facultatif)',
                'choices' => [
                    '★☆☆☆☆ (1)' => 1,
                    '★★☆☆☆ (2)' => 2,
                    '★★★☆☆ (3)' => 3,
                    '★★★★☆ (4)' => 4,
                    '★★★★★ (5)' => 5,
                ],
                'required' => false,
                'placeholder' => '—',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire (facultatif)',
                'required' => false,
                'attr' => ['rows' => 4, 'placeholder' => 'Tu peux détailler ton expérience…'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
