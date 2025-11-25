<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Traslado;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class TrasladoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arrTipoTras = array(
            'Trasladar Activo Fijo dentro de la propiedad y ubicación.'=>'01',
            'Trasladar Activo Fijo en otra propiedad y ubicación.'=>'02',
            'Trasladar Activo Fijo para reparar dentro de la misma propiedad.'=>'03',
            'Trasladar Activo Fijo para reparar en otra propiedad y ubicación.'=>'04',
            'Trasladar Activo Fijo para reparar en una ubicación externa.'=>'05'
        );
        $arrEstatus = [
            'Pendiente' =>  'Pendiente',
            'Aprobado'  =>  'Aprobado',
            'Rechazado' =>  'Rechazado'
        ];

        switch ($options['process'])
        {
            case 'new':
                $builder
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('fecha_traslado',DateType::class,['label'=>'Fecha a Ejecutar Traslado:','widget' => 'single_text','attr'=>['class'=>'form-control',
                'placeholder'=>'...']])
                ->add('tipo_traslado',ChoiceType::class,['label'=>'Tipo Traslado:','choices'=>$arrTipoTras,'data'=>'Nuevo','attr'=>['class'=>'form-control']])
                ->add('id_resp_emisor',TextType::class,['label'=>'Responsable Emisor:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_resp_destino',TextType::class,['label'=>'Responsable Receptor:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_ubic_orig',TextType::class,['label'=>'Ubicación Origen:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_ubic_dest',TextType::class,['label'=>'Ubicación Destino:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('destino_externo_ubic',TextareaType::class,['label'=>'Datos Ubicación Externa:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','requerid'=>false]])
                ->add('destino_externo_info',TextareaType::class,['label'=>'Información Adicional:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('motivo',TextareaType::class,['label'=>'Escriba el motivo por el cual desea trasladar el activo a otra ubicación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('observ',TextareaType::class,['label'=>'Nota del administrador  referente al traslado del activo:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
            ;
            break;
            case 'edit':
                $builder
                ->add('id_traslado',TextType::class,['label'=>'Id Traslado:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('fecha_traslado',DateType::class,['label'=>'Fecha a Ejecutar Traslado:','widget' => 'single_text','attr'=>['class'=>'form-control',
                'placeholder'=>'...']])
                ->add('tipo_traslado',ChoiceType::class,['label'=>'Tipo Traslado:','choices'=>$arrTipoTras,'attr'=>['class'=>'form-control']])
                ->add('id_resp_emisor',TextType::class,['label'=>'Responsable Emisor:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_resp_destino',TextType::class,['label'=>'Responsable Receptor:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_ubic_orig',TextType::class,['label'=>'Ubicación Origen:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_ubic_dest',TextType::class,['label'=>'Ubicación Destino:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('destino_externo_ubic',TextareaType::class,['label'=>'Datos Ubicación Externa:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('destino_externo_info',TextareaType::class,['label'=>'Información Adicional:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('motivo',TextareaType::class,['label'=>'Escriba el motivo por el cual desea trasladar el activo a otra ubicación::','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('observ',TextareaType::class,['label'=>'Nota del administrador  referente al traslado del activo:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
            ;
            break;
            case 'status':
                $builder
                ->add('id_traslado',TextType::class,['label'=>'Id Traslado:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('fecha_traslado',DateType::class,['label'=>'Fecha a Ejecutar Traslado:','widget' => 'single_text','attr'=>['class'=>'form-control',
                'placeholder'=>'...','readonly'=>true]])
                ->add('tipo_traslado',ChoiceType::class,['label'=>'Tipo Traslado:','choices'=>$arrTipoTras,'attr'=>['class'=>'form-control','readonly'=>true]])
                ->add('id_resp_emisor',TextType::class,['label'=>'Responsable Emisor:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_resp_destino',TextType::class,['label'=>'Responsable Receptor:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_ubic_orig',TextType::class,['label'=>'Ubicación Origen:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('id_ubic_dest',TextType::class,['label'=>'Ubicación Destino:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('destino_externo_ubic',TextareaType::class,['label'=>'Datos Ubicación Externa:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('destino_externo_info',TextareaType::class,['label'=>'Información Adicional:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('motivo',TextareaType::class,['label'=>'El motivo por el cual desea trasladar el activo a otra ubicación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('observ',TextareaType::class,['label'=>'Escriba una nota referente al nuevo estado del traslado:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('estatus',ChoiceType::class,['label'=>'Indique el estado del Traslado:','choices'=>$arrEstatus,'attr'=>['class'=>'form-control']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
            ;
            break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Traslado::class,
        ]);

        /* Tipo proceso */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');

    }
}
