<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['isAccount']) {
            $builder->add('oldPassword', PasswordType::class, array(
                'mapped' => false,
                'label' => 'Ancien mot de passe'
            ));
        }
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
            ])
            ->add('submit', SubmitType::class, ['label'=>'Envoyer', 'attr'=>['class'=>'btn-success btn-block']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'isAccount'
        ]);
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
