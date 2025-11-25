<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Mantenimiento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class MantenimientoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arrUnidad = array(
            'Horas'     => 'Horas',
            'Minutos'   => 'Minutos',
            'Días'      => 'Días',
            'Semanas'   => 'Semanas',
        );

        $arrTipoDoc = array(
            'Cheque'        => 'Cheque',
            'Transferencia' => 'Transferencia',
            'Deposito'      => 'Deposito',
        );

        switch ($options['process'])
        {
            case 'new':
                $builder
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_resp',TextType::class,['label'=>'Responsable:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('fecha_fact',DateType::class,['label'=>'Fecha Factura:','widget' => 'single_text','empty_data'=>'','attr'=>['class'=>'form-control','placeholder'=>'...']])
                ->add('nro_fact',TextType::class,['label'=>'Factura Nro.:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('proveedor',TextType::class,['label'=>'Nombre o Razón Social :','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('proveedor_rif',TextType::class,['label'=>'Rif:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('telefono_prov',TextType::class,['label'=>'Teléfono:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('monto_fact',MoneyType::class,['label'=>'Costo:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')"]])
                ->add('monto_iva',MoneyType::class,['label'=>'Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')"]])
                ->add('total_factura',MoneyType::class,['label'=>'Total Factura:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('unidad_tiempo',ChoiceType::class,['label'=>'Tiempo:','choices'=>$arrUnidad,'attr'=>['class'=>'form-control']])
                ->add('numero_tiempo',TextType::class,['label'=>'Número:','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off',"onKeyDown"=>"solo_numero(event)"]])
                ->add('banco',TextType::class,['label'=>'Banco Emisor:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('tipo_doc',ChoiceType::class,['label'=>'Tipo Documento:','choices'=>$arrTipoDoc,'attr'=>['class'=>'form-control']])
                ->add('numero_doc',TextType::class,['label'=>'Número de Documento:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('detalle',TextareaType::class,['label'=>'Detalle de la reparación realizada:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('si_traslado',CheckboxType::class,['label'=>'Incluir costo de Flete:','attr'=>['class'=>'form-control switch','autocomplete'=>'off']])
                ->add('costo_traslado',MoneyType::class,['label'=>'Costo Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                ->add('imp_traslado',MoneyType::class,['label'=>'Flete Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                ->add('total_traslado',MoneyType::class,['label'=>'Total Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','readonly'=>true]])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
            case 'edit':
                $builder
                ->add('id_mant',TextType::class,['label'=>'Id Reparacion:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_resp',TextType::class,['label'=>'Responsable:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('fecha_fact',DateType::class,['label'=>'Fecha Factura:','widget' => 'single_text','empty_data'=>'','attr'=>['class'=>'form-control','placeholder'=>'...']])
                ->add('nro_fact',TextType::class,['label'=>'Factura Nro.:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('proveedor',TextType::class,['label'=>'Nombre o Razón Social :','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('proveedor_rif',TextType::class,['label'=>'Rif:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('telefono_prov',TextType::class,['label'=>'Teléfono:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('monto_fact',MoneyType::class,['label'=>'Costo:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')"]])
                ->add('monto_iva',MoneyType::class,['label'=>'Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')"]])
                ->add('total_factura',MoneyType::class,['label'=>'Total Factura:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('unidad_tiempo',ChoiceType::class,['label'=>'Tiempo:','choices'=>$arrUnidad,'attr'=>['class'=>'form-control']])
                ->add('numero_tiempo',TextType::class,['label'=>'Número:','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off',"onKeyDown"=>"solo_numero(event)"]])
                ->add('banco',TextType::class,['label'=>'Banco Emisor:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('tipo_doc',ChoiceType::class,['label'=>'Tipo Documento:','choices'=>$arrTipoDoc,'attr'=>['class'=>'form-control']])
                ->add('numero_doc',TextType::class,['label'=>'Número de Documento:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('detalle',TextareaType::class,['label'=>'Detalle de la reparación realizada:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('si_traslado',CheckboxType::class,['label'=>'Incluir costo de Flete:','attr'=>['class'=>'form-control switch','autocomplete'=>'off']])
                ->add('costo_traslado',MoneyType::class,['label'=>'Costo Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                ->add('imp_traslado',MoneyType::class,['label'=>'Flete Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                ->add('total_traslado',MoneyType::class,['label'=>'Total Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','readonly'=>true]])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
            case 'mejNew':
                $builder
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_resp',TextType::class,['label'=>'Responsable:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('fecha_fact',DateType::class,['label'=>'Fecha de Registro:','widget' => 'single_text','empty_data'=>'','data'=>new \DateTime(),'attr'=>['class'=>'form-control','placeholder'=>'...']])
                ->add('monto_fact',MoneyType::class,['label'=>'Costo:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('monto_iva',MoneyType::class,['label'=>'Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('total_factura',MoneyType::class,['label'=>'Total Factura:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('detalle',TextareaType::class,['label'=>'Detalle de la reparación realizada:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('si_traslado',CheckboxType::class,['label'=>'Incluir costo de Flete:','attr'=>['class'=>'form-control switch','autocomplete'=>'off']])
                ->add('costo_traslado',MoneyType::class,['label'=>'Costo Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                ->add('imp_traslado',MoneyType::class,['label'=>'Flete Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                ->add('total_traslado',MoneyType::class,['label'=>'Total Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','readonly'=>true]])
                ->add('unidad_tiempo',HiddenType::class,['label'=>false,'data'=>'Horas','attr'=>['class'=>'form-control']])
                ->add('numero_tiempo',HiddenType::class,['label'=>false,'data'=>1,'attr'=>['class'=>'form-control text-right']])
                ->add('banco',TextType::class,['label'=>'Banco Emisor:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('tipo_doc',ChoiceType::class,['label'=>'Tipo Documento:','choices'=>$arrTipoDoc,'attr'=>['class'=>'form-control']])
                ->add('numero_doc',TextType::class,['label'=>'Número de Documento:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('proveedor',HiddenType::class,['label'=>false,'data'=>'sn','attr'=>['class'=>'form-control text-right']])
                ->add('proveedor_rif',HiddenType::class,['label'=>false,'data'=>'sn','attr'=>['class'=>'form-control text-right']])
                ->add('nro_fact',HiddenType::class,['label'=>false,'data'=>'sn','attr'=>['class'=>'form-control text-right']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
            case 'mejEdit':
                $builder
                ->add('id_mant',TextType::class,['label'=>'Id Reparacion:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_resp',TextType::class,['label'=>'Responsable:','attr'=>['class'=>'form-control af-data-strech','placeholder'=>'...','readonly'=>true]])
                ->add('fecha_fact',DateType::class,['label'=>'Fecha de Registro:','widget' => 'single_text','empty_data'=>'','data'=>new \DateTime(),'attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('monto_fact',MoneyType::class,['label'=>'Costo:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('monto_iva',MoneyType::class,['label'=>'Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('total_factura',MoneyType::class,['label'=>'Total Factura:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('detalle',TextareaType::class,['label'=>'Detalle de la reparación realizada:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('si_traslado',CheckboxType::class,['label'=>'Incluir costo de Flete:','attr'=>['class'=>'form-control switch','autocomplete'=>'off']])
                ->add('costo_traslado',MoneyType::class,['label'=>'Costo Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                ->add('imp_traslado',MoneyType::class,['label'=>'Flete Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','onkeyDown'=>"mascara(event,'999999999.99',',','.')",'readonly'=>true]])
                ->add('total_traslado',MoneyType::class,['label'=>'Total Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right','placeholder'=>'...',
                    'autocomplete'=>'off','readonly'=>true]])
                ->add('unidad_tiempo',HiddenType::class,['label'=>false,'data'=>'Horas','attr'=>['class'=>'form-control']])
                ->add('numero_tiempo',HiddenType::class,['label'=>false,'data'=>1,'attr'=>['class'=>'form-control text-right']])
                ->add('banco',TextType::class,['label'=>'Banco Emisor:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('tipo_doc',ChoiceType::class,['label'=>'Tipo Documento:','choices'=>$arrTipoDoc,'attr'=>['class'=>'form-control']])
                ->add('numero_doc',TextType::class,['label'=>'Número de Documento:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>'off']])
                ->add('proveedor',HiddenType::class,['label'=>false,'data'=>'sn','attr'=>['class'=>'form-control text-right']])
                ->add('proveedor_rif',HiddenType::class,['label'=>false,'data'=>'sn','attr'=>['class'=>'form-control text-right']])
                ->add('nro_fact',HiddenType::class,['label'=>false,'data'=>'sn','attr'=>['class'=>'form-control text-right']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
    
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mantenimiento::class,
        ]);

        /* Tipo proceso */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');

    }
}
