<?php
/**
 * Created by PhpStorm.
 * User: saulius.vaitkevicius
 * Date: 4/26/2016
 * Time: 2:31 PM
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class WorkoutType
 * @package AppBundle\Form
 */
class WorkoutType extends AbstractType
{

    /**
     * An array to choose workout's difficulty from
     * @var array
     */
    private $choices = array(
                    1   => 'Labai lengva',
                    2   => 'Lengva',
                    3   => 'Vidutine',
                    4   => 'Sunki',
                    5   => 'Labai sunki'
                );
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Workout'
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('difficulty', ChoiceType::class, array(
                'choices' => $this->choices, 'expanded' => true,
            ))
            ->add('description', TextareaType::class)
            ->add('schedule', CollectionType::class, array(
                'entry_type' => TextareaType::class,
                'required' => false
            ))
            ->add('save', SubmitType::class, array('label' => 'Pridėti programą'))
            ->getForm();
    }

}