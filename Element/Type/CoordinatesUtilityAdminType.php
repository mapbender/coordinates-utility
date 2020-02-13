<?php

namespace Mapbender\CoordinatesUtilityBundle\Element\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Mapbender\CoordinatesUtilityBundle\Element\EventListener\CoordinatesUtilitySubscriber;

class CoordinatesUtilityAdminType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'application' => null
        ]);
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new CoordinatesUtilitySubscriber();
        $builder->addEventSubscriber($subscriber);

        $builder
            ->add('target', 'Mapbender\CoreBundle\Element\Type\TargetElementType',[
                'element_class' => 'Mapbender\\CoreBundle\\Element\\Map',
                'application'   => $options['application'],
                'required'      => false
            ])
            ->add('srsList', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'required' => false
            ])
            ->add('zoomlevel', 'Symfony\Component\Form\Extension\Core\Type\IntegerType',
                [
                    'label' => "Zoom-Level",
                    'empty_data'  => 0,
                    'attr' => [
                        'type' => 'number',
                        'min' => 0
                    ]
                ])
            ->add('addMapSrsList', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => 'mb.coordinatesutility.backend.addMapSrsList',
                'required' => false,
            ])
        ;
    }
}
