<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Responsable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResponsableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['process']){
            case 'new':
                $builder
                ->add('nombre',TextType::class,['label'=>'Nombres:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('apellido',TextType::class,['label'=>'Apellidos:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('cargo',TextType::class,['label'=>'Cargo:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('telefono',TextType::class,['label'=>'Teléfono:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('movil',TextType::class,['label'=>'Móvil:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
            case 'edit':
                $builder
                ->add('id_resp',TextType::class,['label'=>'Id Responsable:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('nombre',TextType::class,['label'=>'Nombres:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('apellido',TextType::class,['label'=>'Apellidos:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('cargo',TextType::class,['label'=>'Cargo:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('telefono',TextType::class,['label'=>'Teléfono:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('movil',TextType::class,['label'=>'Móvil:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Responsable::class,
        ]);
        
        /* Tipo de Proceso a validar */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');

    }
}
