<?php

namespace App\Form\Admin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $options): void
    {
        $b
            ->add('email', EmailType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
                'label' => 'Email',
            ])
            ->add('username', TextType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max:60)],
                'label' => 'Pseudo',
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'constraints' => [new Assert\NotBlank(), new Assert\Length(min:8)],
                'label' => 'Mot de passe',
            ]);
    }
}
