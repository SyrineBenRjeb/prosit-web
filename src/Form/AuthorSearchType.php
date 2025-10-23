<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AuthorSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minBooks', IntegerType::class, [
                'label' => 'Nombre minimum de livres',
                'required' => false,
            ])
            ->add('maxBooks', IntegerType::class, [
                'label' => 'Nombre maximum de livres',
                'required' => false,
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
            ]);
    }
}
