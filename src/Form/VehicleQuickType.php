<?php

namespace App\Form;

use App\Entity\VehicleEnergies;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class VehicleQuickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 60),
                    new Assert\NotBlank(groups: ['quick']),
                ],
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 60),
                    new Assert\NotBlank(groups: ['quick']),
                ],
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur',
                'required' => false,
                'constraints' => [new Assert\Length(max: 20)],
            ])
            ->add('seats', IntegerType::class, [
                'label' => 'Places',
                'required' => false,
                'constraints' => [
                    new Assert\Positive(groups: ['quick']),
                    new Assert\NotBlank(groups: ['quick']),
                ],
                'attr' => ['min' => 1, 'step' => 1],
            ])
            ->add('plate', TextType::class, [
                'label' => 'Immatriculation',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 20),
                    new Assert\NotBlank(groups: ['quick']),
                ],
            ])
            ->add('firstRegistration', DateType::class, [
                'label' => 'Première immatriculation',
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(groups: ['quick']),
                ],
            ])
            ->add('energy', EntityType::class, [
                'class' => VehicleEnergies::class,
                'choice_label' => 'label',
                'choice_value' => 'id',
                'label' => 'Énergie',
                'placeholder' => 'Choisir…',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(groups: ['quick']),
                ],
            ]);
    }
}
