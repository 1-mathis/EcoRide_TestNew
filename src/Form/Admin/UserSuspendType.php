<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class UserSuspendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $options): void
    {
        $b->add('reason', TextareaType::class, [
            'required' => false,
            'label' => 'Raison (optionnelle)',
            'constraints' => [new Assert\Length(max: 1000)],
        ]);
    }
}
