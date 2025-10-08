<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class DriverPreferencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('smokerAllowed',  CheckboxType::class, [
                'label' => 'Accepte les fumeurs',
                'required' => false,
                'mapped' => false,
            ])
          ->add('animalsAllowed', CheckboxType::class, [
                'label' => 'Accepte les animaux',
                'required' => false,
                'mapped' => false,
            ]);
    }
}
