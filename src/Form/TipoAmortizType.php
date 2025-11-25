<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\TipoAmortiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TipoAmortizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arrFormula = array(
            "MÉTODO DECRECIENTE"=>"MÉTODO DECRECIENTE",
            "MÉTODO CRECIENTE"=>"MÉTODO CRECIENTE",
            "LINEA RECTA"=>"LINEA RECTA",
        );

        switch ($options['process']){
            case 'define':
                $builder
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('formula',ChoiceType::class,['label'=>'Fórmula:','choices'=>$arrFormula,'attr'=>['class'=>'form-control']])
                ->add('tiempo_estimado',TextType::class,['label'=>'Tiempo estimado:','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','autocomplete'=>"off","onKeyDown"=>"solo_numero(event)"]])
                ->add('valor_salvamento',MoneyType::class,['label'=>'Valor de Salvamento:','data'=>'0.00','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off"]])
                ->add('observ',TextType::class,['label'=>'Observación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off"]])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
    
                break;
            case 'edit':
                $builder
                ->add('id_tipom',TextType::class,['label'=>'Secuencia :','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('id_af',TextType::class,['label'=>'Id Activo Fijo:','attr'=>['class'=>'form-control','placeholder'=>'...','readonly'=>true]])
                ->add('formula',ChoiceType::class,['label'=>'Fórmula:','choices'=>$arrFormula,'attr'=>['class'=>'form-control']])
                ->add('tiempo_estimado',TextType::class,['label'=>'Tiempo estimado:','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','autocomplete'=>"off","onKeyDown"=>"solo_numero(event)"]])
                ->add('valor_salvamento',MoneyType::class,['label'=>'Valor de Salvamento:','grouping'=>true,'currency'=>'','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','onKeyDown'=>"mascara(event,'999999999.99',',','.')",'autocomplete'=>"off"]])
                ->add('observ',TextType::class,['label'=>'Observación:','attr'=>['class'=>'form-control','placeholder'=>'...','autocomplete'=>"off"]])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;

                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TipoAmortiz::class,
        ]);

        /* Tipo proceso */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');
    }
}
