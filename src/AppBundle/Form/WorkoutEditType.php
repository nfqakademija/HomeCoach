<?php
/**
 * Created by PhpStorm.
 * User: darius0021
 * Date: 16.5.6
 * Time: 20.18
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WorkoutEditType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('edit', SubmitType::class, array(
                'label' => "Redaguoti",))
            ->add('delete', SubmitType::class, array(
                'label' => "Ištrinti",))
            ->getForm();
    }
}