<?php

namespace App\Form;

use App\Entity\Vehicle;
use App\Entity\VehicleEnergies;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, [
                'label'       => 'Marque',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 60),
                ],
            ])
            ->add('model', TextType::class, [
                'label'       => 'Modèle',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 60),
                ],
            ])
            ->add('color', TextType::class, [
                'label'       => 'Couleur',
                'required'    => false,
                'constraints' => [
                    new Assert\Length(max: 20),
                ],
            ])
            ->add('seats', IntegerType::class, [
                'label'       => 'Nombre de places',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                ],
            ])
            ->add('plate', TextType::class, [
                'label'       => 'Plaque d’immatriculation',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 20),
                ],
            ])
            ->add('firstRegistration', DateType::class, [
                'label'       => 'Date de 1ère immatriculation',
                'widget'      => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('energy', EntityType::class, [
                'class'        => VehicleEnergies::class,
                'choice_label' => 'label',
                'label'        => 'Énergie',
                'placeholder'  => 'Choisir…',
                'constraints'  => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
