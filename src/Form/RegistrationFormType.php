<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail'
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne sont pas identiques',
                'options' => ['attr' => ['autocomplete' => 'new-password'] ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un mot de passe'
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} characteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096
                    ])
                ],
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer mot de passe']
            ])
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('isKine', CheckboxType::class, [
                'label' => 'Êtes-vous un professionnel ?',
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false
        ]);
    }
}
