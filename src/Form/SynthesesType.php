<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SynthesesType extends AbstractType
{
    const INVOICE_BY_MEMBER = 0;
    const PRODUCT_BY_MEMBER = 1;
    const INVOICE_BY_PRODUCER = 2;
    const INVOICE_BY_PRODUCER_BY_MEMBER = 3;
    const LABELS = [
        self::INVOICE_BY_MEMBER => 'Facture des consom\'acteurs/trices',
        self::PRODUCT_BY_MEMBER => 'Paniers des consom\'acteurs/trices',
        self::INVOICE_BY_PRODUCER => 'Pointage permanence et commande des producteurs/trices',
        self::INVOICE_BY_PRODUCER_BY_MEMBER => 'Pointage paiement des consom\'acteurs/trices',
    ];
    const LABELS_LIGHT = [
        self::INVOICE_BY_MEMBER => 'Facture des consom\'acteurs/trices',
        self::PRODUCT_BY_MEMBER => 'Paniers des consom\'acteurs/trices',
        self::INVOICE_BY_PRODUCER => 'Pointage permanence et commande des producteurs/trices',
    ];
    const FILES = [
        self::INVOICE_BY_MEMBER => 'facture_des_consomacteurs_trices',
        self::PRODUCT_BY_MEMBER => 'paniers_des_consom_acteurs_trices',
        self::INVOICE_BY_PRODUCER => 'commande_des_producteurs_trices',
        self::INVOICE_BY_PRODUCER_BY_MEMBER => 'pointage_paiement_des_consom_acteurs_trices',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => ['autocomplete' => 'off', 'class' => 'date-picker'],
            ])
            ->add('end', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => ['autocomplete' => 'off', 'class' => 'date-picker'],
            ]);
        if ($options['light']) {
            $builder->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => array_flip(self::LABELS_LIGHT),
            ]);
        } else {
            $builder->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => array_flip(self::LABELS),
            ]);
        }
        $builder->add('submit', SubmitType::class, ['label' => 'Extraire', 'attr' => ['class' => 'btn-success btn-block']])
        ;
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            $builder = $event->getForm();
            $type = $event->getData()['type'] ?? null;
            if (null !== $type) {
                if (!$options['light'] && (self::INVOICE_BY_MEMBER == $type || self::INVOICE_BY_PRODUCER == $type || self::INVOICE_BY_PRODUCER_BY_MEMBER == $type)) {
                    $builder->add('email', FormatEmailType::class, ['label' => false, 'attr' => ['class' => 'form-popin']]);
                }
                $builder
                    ->add('css', ChoiceType::class, [
                        'label' => false,
                        'empty_data' => 'pdf-black-inline',
                        'choices' => [
                            'en noir et blanc sans saut de page' => 'pdf-black-inline',
                            'en noir et blanc avec sauts de page' => 'pdf-black-page-break',
                            'en couleur sans saut de page' => 'pdf-color-inline',
                            'en couleur avec sauts de page' => 'pdf-color-page-break',
                        ],
                        'expanded' => true,
                        'attr' => ['class' => 'css form-popin'],
                        'label_attr' => ['class' => 'form-popin'],
                    ])
                    ->add('pdf', SubmitType::class, ['label' => 'Imprimer', 'attr' => [
                        'class' => 'btn-success btn-block download-file',
                        'data-sub-form' => '.css',
                    ]]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('allow_extra_fields', true);
        $resolver->setDefaults([
            'light' => false,
        ]);
    }
}
