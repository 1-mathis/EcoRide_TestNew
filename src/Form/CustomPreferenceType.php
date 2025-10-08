<?php

namespace App\Form;

use App\Entity\DriverPreferences;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class CustomPreferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('keyName', TextType::class, [
                'label' => 'Nom de la préférence',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max:60)],
                'attr' => ['placeholder' => 'ex: musique, clim, bagages…'],
            ])
          ->add('valueText', TextType::class, [
                'label' => 'Valeur',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max:120)],
                'attr' => ['placeholder' => 'ex: oui, non, faible…'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => DriverPreferences::class]);
    }
}
