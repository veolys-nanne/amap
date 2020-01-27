<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PreviewEmailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = [];

        foreach ($builder->getData()['messages']  as $message) {
            $data[] = [
                'to' => array_keys($message->getBcc()),
                'subject' => $message->getSubject(),
                'body' => $message->getBody(),
                'part' => $message->getChildren()[0]->getBody(),
            ];
        }
        $builder
            ->add('messages', CollectionType::class, [
                'label' => false,
                'entry_type' => PreviewEmailType::class,
                'entry_options' => ['label' => false],
                'data' => $data,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']]);
    }
}
