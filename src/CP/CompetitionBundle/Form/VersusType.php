<?php

namespace CP\CompetitionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VersusType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('scorej1', 'integer')
            ->add('scorej2', 'integer')
            ->add('screenshot', new ScreenshotType())
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CP\CompetitionBundle\Entity\Versus'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cp_competitionbundle_versus';
    }
}
