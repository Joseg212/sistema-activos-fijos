<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\Activofijo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ActivoFijoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arrEdoFisico = array("Nuevo"=>"Nuevo","Usado"=>"Usuado");
        $arrClases = $options['clases'];

        switch ($options['process']){
            case 'new';
                $builder
                ->add('id_ubic',TextType::class,['label'=>'Id Ubicación:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('code_activof',TextType::class,['label'=>'Código Asignado:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_clase',ChoiceType::class,['label'=>'Tipo:','choices'=>$arrClases,'data'=>'Nuevo','attr'=>['class'=>'form-control']])
                ->add('descrip',TextType::class,['label'=>'Descripción:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off"]])
                ->add('fecha_compra',DateType::class,['label'=>'Fecha de Compra:','widget' => 'single_text','attr'=>['class'=>'form-control',
                    'placeholder'=>'...','onKeyDown'=>"mascara('XX/XX/XXXX','','')"]])
                ->add('nrofact',TextType::class,['label'=>'Nro Factura:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off"]])
                ->add('distribuidor',TextType::class,['label'=>'Distribuidor:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off"]])
                ->add('rif',TextType::class,['label'=>'RIF:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off","onKeyDown"=>"solo_texto(event)"]])
                ->add('costo',MoneyType::class,['label'=>'Costo:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                    'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off"]])
                ->add('impuesto',MoneyType::class,['label'=>'Impuesto:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off"]])
                ->add('costo_total',MoneyType::class,['label'=>'Costo Total:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off",'readonly'=>true]])
                ->add('costo_flete',MoneyType::class,['label'=>'Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off"]])
                ->add('num_serie',TextType::class,['label'=>'Número de serie.:','attr'=>['class'=>'form-control',
                'placeholder'=>'...','onKeyDown'=>"solo_texto(event)",'autocomplete'=>"off"]])
                ->add('edo_fisico',ChoiceType::class,['label'=>'Tipo:','choices'=>$arrEdoFisico,'attr'=>['class'=>'form-control']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
            case 'edit';
                $builder
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_ubic',TextType::class,['label'=>'Id Ubicación:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('code_activof',TextType::class,['label'=>'Código Asignado:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_clase',ChoiceType::class,['label'=>'Tipo:','choices'=>$arrClases,'attr'=>['class'=>'form-control']])
                ->add('descrip',TextType::class,['label'=>'Descripción:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off"]])
                ->add('fecha_compra',DateType::class,['label'=>'Fecha de Compra:','widget' => 'single_text','attr'=>['class'=>'form-control',
                    'placeholder'=>'...','onKeyDown'=>"mascara('XX/XX/XXXX','','')"]])
                ->add('nrofact',TextType::class,['label'=>'Nro Factura:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off"]])
                ->add('distribuidor',TextType::class,['label'=>'Distribuidor:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off"]])
                ->add('rif',TextType::class,['label'=>'RIF:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off","onKeyDown"=>"solo_texto(event)"]])
                ->add('costo',MoneyType::class,['label'=>'Costo:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                    'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off","readonly"=>true]])
                ->add('impuesto',MoneyType::class,['label'=>'Impuesto:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off","readonly"=>true]])
                ->add('costo_total',MoneyType::class,['label'=>'Costo Total:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off",'readonly'=>true]])
                ->add('costo_flete',MoneyType::class,['label'=>'Flete:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off"]])
                ->add('num_serie',TextType::class,['label'=>'Número de serie.:','attr'=>['class'=>'form-control',
                'placeholder'=>'...','onKeyDown'=>"solo_texto(event)",'autocomplete'=>"off"]])
                ->add('edo_fisico',ChoiceType::class,['label'=>'Tipo:','choices'=>$arrEdoFisico,'attr'=>['class'=>'form-control']])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activofijo::class,
        ]);
        /* Tipo proceso */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');

        /* Array de Clase */
        $resolver->setRequired('clases'); 
        $resolver->setAllowedTypes('clases', 'array');

    }
}
