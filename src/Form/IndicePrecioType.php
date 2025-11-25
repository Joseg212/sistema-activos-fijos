<?php

namespace App\Form;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use App\Entity\IndicePrecio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class IndicePrecioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['process']){
            case 'new':
                $builder
                ->add('anio',TextType::class,['label'=>'Año:','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'XXXX','','')"]])
                ->add('mes',TextType::class,['label'=>'Mes:','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'XX','','')"]])
                ->add('factor',NumberType::class,['label'=>'Factor:','scale'=>6,'attr'=>['class'=>'form-control',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'99999999,999999','.',',')"]])
                ->add('reconver',NumberType::class,['label'=>'Valor Reconversión:','scale'=>2,'attr'=>['class'=>'form-control',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'999999999,99','.',',')"]])
                ->add('grupo',NumberType::class,['label'=>'Grupo:','scale'=>0,'attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'X','','')"]])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
            case 'edit':
                $builder
                ->add('id_ipc',TextType::class,['label'=>'Id IPC:','attr'=>['class'=>'form-control',
                'placeholder'=>'...','autocomplete'=>'off','readonly'=>true]])
                ->add('anio',TextType::class,['label'=>'Año:','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'XXXX','','')"]])
                ->add('mes',TextType::class,['label'=>'Mes:','attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'XX','','')"]])
                ->add('factor',NumberType::class,['label'=>'Factor:','scale'=>6,'attr'=>['class'=>'form-control',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'99999999,999999','.',',')"]])
                ->add('reconver',NumberType::class,['label'=>'Valor Reconversión:','scale'=>2,'attr'=>['class'=>'form-control',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'999999999,99','.',',')"]])
                ->add('grupo',NumberType::class,['label'=>'Grupo:','scale'=>0,'attr'=>['class'=>'form-control text-right',
                'placeholder'=>'...','autocomplete'=>'off','onKeyDown'=>"mascara(event,'X','','')"]])
                ->add('write',SubmitType::class,['label'=>'Guardar','attr'=>['class'=>'btn btn-dark']])
                ;
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IndicePrecio::class,
        ]);

        /* Tipo de Proceso a validar */
        $resolver->setRequired('process'); 
        $resolver->setAllowedTypes('process', 'string');

    }
}
