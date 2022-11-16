<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    "required" => true
                ])

            ->add(
                'area',
                ChoiceType::class,
                [
                    "required" => true,
                    'choices'  => [
                        'Landwirtschaft' => 'Agrarunternehmen',
                        'Handel' => 'Großhandelsunternehmen',
                        'Bauindustrie' => 'Bauunternehmen',
                        'Biotech' => 'Biotechnologieunternehmen',
                        'Dienstleistung' => 'Dienstleistungsunternehmen',
                        'Energie' => 'Energieversorger',
                        'Finanzmärkte' => 'Beteiligungsgesellschaft',
                        'Forschung' => 'Forschungsunternehmen',
                        'Fotografie' => 'Fotounternehmen',
                        'Gesundheit' => 'Hersteller von medizinischen Geräten',
                        'Nuklear' => 'Nuklearunternehmen',
                        'Recycling' => 'Entsorgungsunternehmen',
                        'Kunst' => 'Theaterbetrieb',
                        'Rohstoffe' => 'Mineralölunternehmen',
                        'Luftfahrt' => 'Fluggesellschaft',
                        'Nachrichten' => 'Nachrichtenagentur',
                        'Medien' => 'Medienunternehmen',
                        'Nanotech' => 'Nanotechnologieunternehmen',
                        'IT' => 'Informationstechnikunternehmen',
                        'Sportarten' => 'Sportartikelhersteller',
                        'Raum' => 'Raumfahrtunternehmen',
                        'Mode' => 'Unternehmen',
                        'Marketing' => 'Unternehmen mit Direktmarketing',
                        'Automobile' => 'Automobilzulieferer',
                        ]
                ])

            ->add(
                'city',
                TextType::class,
                [
                    "required" => true
                ])

            ->add(
                'region',
                TextType::class,
                [
                    "required" => true
                ])

            ->add(
                'country',
                TextType::class,
                [
                    "required" => true
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
