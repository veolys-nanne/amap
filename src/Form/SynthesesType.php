<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => array_flip(self::LABELS),
            ])
            ->add('credit', MassCreditType::class, [
                'label' => false,
                'attr' => ['class' => 'hidden credit'],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Extraire', 'attr' => ['class' => 'btn-success btn-block']])
            ->add('submitCredit', SubmitType::class, ['label' => 'Générer', 'attr' => ['class' => 'btn-success btn-block hidden']])
        ;
        if (null !== $options['type'] && (self::INVOICE_BY_MEMBER == $options['type'] || self::INVOICE_BY_PRODUCER == $options['type'] || self::INVOICE_BY_PRODUCER_BY_MEMBER == $options['type'])) {
            $builder
                ->add('email', SubmitType::class, ['label' => 'Envoyer', 'attr' => [
                    'class' => 'btn-success btn-block mail-extra',
                    'data-mail-title' => self::LABELS[$options['type']],
                ]]);
        }
        if (null !== $options['type']) {
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
                    'attr' => ['class' => 'hidden css'],
                ])
                ->add('pdf', SubmitType::class, ['label' => 'Imprimer', 'attr' => [
                    'class' => 'btn-success btn-block form-popin download-file',
                    'data-target' => '.css',
                ]]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'type',
        ]);
        $resolver->setDefault('allow_extra_fields', true);
    }
}
