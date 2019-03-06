<?php

namespace Mapbender\CoordinatesUtilityBundle\Element\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mapbender\CoordinatesUtilityBundle\Element\EventListener\CoordinatesUtilitySubscriber;

class CoordinatesUtilityAdminType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'CoordinatesUtility';
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
            ->add('title', 'text', [
                'required' => false
            ])
            ->add('type', 'choice', [
                'required' => true,
                'choices'  => [
                    'element' => 'Element',
                    'dialog'  => 'Dialog'
                ]
            ])
            ->add('target', 'target_element',[
                'element_class' => 'Mapbender\\CoreBundle\\Element\\Map',
                'application'   => $options['application'],
                'property_path' => '[target]',
                'required'      => false
            ])
            ->add('srsList', 'text', [
                'required' => false
            ])
            ->add('addMapSrsList', 'checkbox', [
                'required' => false
            ])
            ->add('zoomlevel', 'integer',
                [
                    'label' => "Zoom-Level",
                    'empty_data'  => 0,
                    'attr' => [
                        'type' => 'number',
                        'min' => 0
                    ]
                ])
        ;
    }
}
