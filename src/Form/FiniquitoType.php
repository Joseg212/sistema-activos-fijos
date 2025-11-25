<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Finiquito;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FiniquitoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arrTipoFin =['Venta'=>'Venta','Obsoleto'=>'Obsoleto'];

        switch ($options['process']) {
            case 'process_normal':
                $builder
                    ->add('id_fin',HiddenType::class,['label'=>'Id Finiquito:','data'=>'0','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                    ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                    ->add('tipo_finiquito',ChoiceType::class,['label'=>'Finiquito por:','choices'=>$arrTipoFin,'attr'=>['class'=>'form-control']])
                    ->add('fecha_finiquito',DateType::class,['label'=>'Fecha del Finiquito:','widget' => 'single_text','empty_data'=>'','attr'=>['class'=>'form-control','placeholder'=>'...']])
                    ->add('costo_actual',MoneyType::class,['label'=>'Costo Actual:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('factor_inflac',MoneyType::class,['label'=>'Factor Inflacionario:','data'=>'0.000000','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('costo_ajustado',MoneyType::class,['label'=>'Costo Reajustado:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('dep_acumulada',MoneyType::class,['label'=>'Depreciación Acumulada Ajustada:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('monto_venta',MoneyType::class,['label'=>'Vendido por:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')"]])
                    ->add('ganancia_vta',MoneyType::class,['label'=>'Ganancia en Activo Fijo:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('perdida_vta',MoneyType::class,['label'=>'Pérdida en Activo Fijo:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('costo_mej',MoneyType::class,['label'=>'Costo:','grouping'=>true,'data'=>'0.00','currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('imp_mej',MoneyType::class,['label'=>'Impuesto:','grouping'=>true,'data'=>'0.00','currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('total_mej',MoneyType::class,['label'=>'Total:','grouping'=>true,'data'=>'0.00','currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('costo_flete',MoneyType::class,['label'=>'Costo:','grouping'=>true,'data'=>'0.00','currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('imp_flete',MoneyType::class,['label'=>'Impuesto:','grouping'=>true,'data'=>'0.00','currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('total_flete',MoneyType::class,['label'=>'Total:','grouping'=>true,'data'=>'0.00','currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('nueva_descrip',TextType::class,['label'=>'Nueva Descripción:','data'=>' ','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('observacion',TextareaType::class,['label'=>'Observacion o detalle:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                    ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                    ;
                break;
            case 'process_mejora':
                $builder
                    ->add('id_fin',HiddenType::class,['label'=>'Id Finiquito:','data'=>'0','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                    ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                    ->add('tipo_finiquito',TextType::class,['label'=>'Finiquito por:','data'=>'Mejora','attr'=>['class'=>'form-control','readonly'=>true]])
                    ->add('fecha_finiquito',DateType::class,['label'=>'Fecha del Finiquito:','widget' => 'single_text','empty_data'=>'','attr'=>['class'=>'form-control','placeholder'=>'...']])
                    ->add('costo_actual',MoneyType::class,['label'=>'Costo Actual:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('factor_inflac',MoneyType::class,['label'=>'Factor Inflacionario:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('costo_ajustado',MoneyType::class,['label'=>'Costo Reajustado:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('dep_acumulada',MoneyType::class,['label'=>'Depreciación Acumulada Ajustada:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('monto_venta',MoneyType::class,['label'=>'Vendido por:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                    ->add('ganancia_vta',MoneyType::class,['label'=>'Ganancia en Activo Fijo:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('perdida_vta',MoneyType::class,['label'=>'Pérdida en Activo Fijo:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('costo_mej',MoneyType::class,['label'=>'Costo:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('imp_mej',MoneyType::class,['label'=>'Impuesto:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('total_mej',MoneyType::class,['label'=>'Total:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('costo_flete',MoneyType::class,['label'=>'Costo:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('imp_flete',MoneyType::class,['label'=>'Impuesto:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('total_flete',MoneyType::class,['label'=>'Total:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                    ->add('nueva_descrip',TextType::class,['label'=>'Nueva Descripción:','data'=>' ','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                    ->add('observacion',TextareaType::class,['label'=>'Observacion o detalle:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                    ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                    ;
                break;

        }


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Finiquito::class,
        ]);

        /* Tipo proceso */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');
    }
}
