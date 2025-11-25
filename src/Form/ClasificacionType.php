<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Clasificacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ClasificacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['process']){
            case 'new':
                $builder
                ->add('descripcion',TextType::class,['label'=>'Descripción:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('cod_cuenta',TextType::class,['label'=>'Cuenta Contable:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ->add('observacion',TextType::class,['label'=>'Observación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ;
                break;
            case 'edit':
                $builder
                ->add('id_clase',TextType::class,['label'=>'Id Clasificación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('descripcion',TextType::class,['label'=>'Descripción:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('cod_cuenta',TextType::class,['label'=>'Cuenta Contable:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ->add('observacion',TextType::class,['label'=>'Observación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ;
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clasificacion::class,
        ]);

        /* Tipo de Proceso a validar */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');

    }
}
