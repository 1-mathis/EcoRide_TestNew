<?php

namespace App\Form;

use App\Entity\CarSharings;
use App\Entity\Vehicle;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CarSharingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $driver */
        $driver = $options['driver'];

        $builder
            ->add('fromCity', TextType::class, [
                'label' => 'Ville de départ',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 120)],
            ])
            ->add('toCity', TextType::class, [
                'label' => 'Ville d’arrivée',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(max: 120)],
            ])
            ->add('departureAt', DateTimeType::class, [
                'label' => 'Départ',
                'widget' => 'single_text',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('arrivalAt', DateTimeType::class, [
                'label' => 'Arrivée (estimée)',
                'widget' => 'single_text',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('durationMinutes', IntegerType::class, [
                'label' => 'Durée (minutes)',
                'constraints' => [new Assert\NotBlank(), new Assert\PositiveOrZero()],
                'attr' => ['min' => 0, 'step' => 1],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix (crédits)',
                'html5' => true,
                'scale' => 2,
                'constraints' => [new Assert\NotBlank(), new Assert\GreaterThanOrEqual(0)],
                'help' => '⚠️ 2 crédits seront prélevés par la plateforme lors de la réservation.',
                'attr' => ['min' => 0, 'step' => '0.01'],
            ])
            ->add('seatsTotal', IntegerType::class, [
                'label' => 'Places totales',
                'constraints' => [new Assert\NotBlank(), new Assert\PositiveOrZero()],
                'attr' => ['min' => 0, 'step' => 1],
            ])
            ->add('isEco', CheckboxType::class, [
                'label' => 'Trajet écologique (véhicule électrique)',
                'required' => false,
            ])
            ->add('vehicleId', EntityType::class, [
                'class' => Vehicle::class,
                'label' => 'Véhicule',
                'placeholder' => 'Sélectionner un véhicule…',
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($driver) {
                    return $er->createQueryBuilder('v')
                        ->andWhere('v.ownerId = :owner')
                        ->setParameter('owner', $driver)
                        ->orderBy('v.brand', 'ASC');
                },
                'choice_label' => fn (Vehicle $v) => sprintf('%s %s (%s • %d pl.)', $v->getBrand(), $v->getModel(), $v->getPlate(), $v->getSeats()),
            ])
            ->add('addVehicle', CheckboxType::class, [
                'label' => 'Ajouter un nouveau véhicule',
                'mapped' => false,
                'required' => false,
            ])
            ->add('vehicleQuick', VehicleQuickType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('status', HiddenType::class, [
                'data' => 'published',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('driver');
        $resolver->setAllowedTypes('driver', \App\Entity\User::class);

        $resolver->setDefaults([
            'data_class' => CarSharings::class,
            'validation_groups' => function (FormInterface $form) {
                $groups = ['Default'];
                $addVehicle = $form->has('addVehicle') ? (bool) $form->get('addVehicle')->getData() : false;
                if ($addVehicle) {
                    $groups[] = 'quick';
                }
                return $groups;
            },
        ]);
    }
}
