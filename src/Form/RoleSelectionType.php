<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class RoleSelectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('driver', CheckboxType::class, [
                'label' => 'Je suis chauffeur', 'required' => false, 'mapped' => false,
            ])
          ->add('passenger', CheckboxType::class, [
                'label' => 'Je suis passager', 'required' => false, 'mapped' => false,
            ]);
    }
}
