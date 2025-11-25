<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Ubicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UbicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch($options['process'])
        {
            case 'new':
                $builder
                ->add('id_propiedad',TextType::class,['label'=>'Id Propiedad:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('ubicacion',TextType::class,['label'=>'Ubicación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('nota',TextType::class,['label'=>'Nota:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
            case 'edit':
                $builder
                ->add('id_ubic',TextType::class,['label'=>'Id Ubicación:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_propiedad',TextType::class,['label'=>'Id Propiedad:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('ubicacion',TextType::class,['label'=>'Ubicación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('nota',TextType::class,['label'=>'Nota:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ubicacion::class,
        ]);
        /* Tipo proceso */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');

    }
}
