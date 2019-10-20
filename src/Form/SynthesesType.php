<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SynthesesType extends AbstractType
{
    const INVOICE_BY_MEMBER = 0;
    const PRODUCT_BY_PRODUCER = 1;
    const PRODUCT_BY_MEMBER = 2;
    const INVOICE_BY_PRODUCER = 3;
    const INVOICE_BY_PRODUCER_BY_MEMBER = 4;
    const LABELS = [
        self::INVOICE_BY_MEMBER => 'Facture des consom\'acteurs/trices',
        self::PRODUCT_BY_PRODUCER => 'Pointage permanence des producteurs/trices',
        self::PRODUCT_BY_MEMBER => 'Paniers des consom\'acteurs/trices',
        self::INVOICE_BY_PRODUCER => 'Commande des producteurs/trices',
        self::INVOICE_BY_PRODUCER_BY_MEMBER => 'Pointage paiement des consom\'acteurs/trices',
    ];
    const FILES = [
        self::INVOICE_BY_MEMBER => 'facture_des_consomacteurs_trices',
        self::PRODUCT_BY_PRODUCER => 'pointage_permanence_des_producteurs_trices',
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
                'choices' => [
                    self::LABELS[self::INVOICE_BY_MEMBER] => self::INVOICE_BY_MEMBER,
                    self::LABELS[self::PRODUCT_BY_PRODUCER] => self::PRODUCT_BY_PRODUCER,
                    self::LABELS[self::PRODUCT_BY_MEMBER] => self::PRODUCT_BY_MEMBER,
                    self::LABELS[self::INVOICE_BY_PRODUCER] => self::INVOICE_BY_PRODUCER,
                    self::LABELS[self::INVOICE_BY_PRODUCER_BY_MEMBER] => self::INVOICE_BY_PRODUCER_BY_MEMBER,
                ],
            ])
            ->add('submit', SubmitType::class, ['label'=>'Extraire', 'attr'=>['class'=>'btn-success btn-block']]);
        if (null !== $options['type'] && ($options['type'] == self::INVOICE_BY_MEMBER || $options['type'] == self::INVOICE_BY_PRODUCER || $options['type'] == self::INVOICE_BY_PRODUCER_BY_MEMBER)) {
            $builder
                ->add('email', SubmitType::class, ['label' => 'Envoyer', 'attr' => [
                    'class' => 'btn-success btn-block mail-extra',
                    'data-mail-title' => self::LABELS[$options['type']],
                ]]);
        }
        if (null !== $options['type']) {
            $builder
                ->add('css', HiddenType::class, [
                    'data' => 'pdf-color-page-break',
                ])
                ->add('pdf', SubmitType::class, ['label' => 'Imprimer', 'attr' => [
                    'class' => 'btn-success btn-block',
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
