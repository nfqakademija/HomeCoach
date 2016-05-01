<?php
/**
 * Created by PhpStorm.
 * User: darius0021
 * Date: 16.5.1
 * Time: 19.03
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ActivateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $label = "Aktyvuoti";
        if ($options['disabled']) {
            $label="Programa aktyvuota!";
        }
        $builder
            ->add('activate', SubmitType::class, array(
                'label' => $label,
                'disabled' => $options['disabled']))
            ->getForm();
    }
}
