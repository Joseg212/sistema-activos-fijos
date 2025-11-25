<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Propiedad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PropiedadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arrTipo = array(
            'Oficina'=>'Oficina',
            'Local Comercial'=>'Local Comercial',
            'Galpón'=>'Galpón',
            'Edificio'=>'Edificio',
        );

        switch ($options['process']){
            case 'new':
                $builder
                ->add('tipo',ChoiceType::class,['label'=>'Tipo:','choices'=>$arrTipo,'data'=>'Oficina','attr'=>['class'=>'form-control']])
                ->add('nombre',TextType::class, ['label'=>'Descripción:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('encargado',TextType::class,['label'=>'Encargado:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('direccion',TextType::class,['label'=>'Dirección:','attr'=>['class'=>'form-control','placeholder'=>'....','autocomplete'=>'off']])
                ->add('nota',TextType::class,['label'=>'Nota:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('telefono',TextType::class,['label'=>'Teléfono:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('movil',TextType::class,['label'=>'Móvil:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('Write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                    ;
    
                break;
            case 'edit':
                $builder
                ->add('id_propiedad',TextType::class, ['label'=>'Id Propiedad:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('tipo',ChoiceType::class,['label'=>'Tipo:','choices'=>$arrTipo,'attr'=>['class'=>'form-control']])
                ->add('nombre',TextType::class, ['label'=>'Descripción:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('encargado',TextType::class,['label'=>'Encargado:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('direccion',TextType::class,['label'=>'Dirección:','attr'=>['class'=>'form-control','placeholder'=>'....','autocomplete'=>'off']])
                ->add('nota',TextType::class,['label'=>'Nota:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('telefono',TextType::class,['label'=>'Teléfono:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('movil',TextType::class,['label'=>'Móvil:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('Write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                    ;
                break;
        }


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Propiedad::class,
        ]);

        /* Tipo de Proceso a validar */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');
        
    }
}
