<?php
namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $document = $event->getData();
            $builder = $event->getForm();
            $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'disabled' => null !== $document->getId(),
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Role',
                'required' => false,
                'disabled' => null !== $document->getId(),
                'choices' => [
                    'administrateur/trice' => 'ROLE_ADMIN',
                    'référent/e' => 'ROLE_REFERENT',
                    'producteur/trice' => 'ROLE_PRODUCER',
                    'consom\'acteur/trice' => 'ROLE_MEMBER',
            ]]);
        });

        $builder
            ->add('text', TextareaType::class, ['label' => 'Texte'])
            ->add('submit', SubmitType::class, ['label'=>'Envoyer', 'attr'=>['class'=>'btn-success btn-block']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
