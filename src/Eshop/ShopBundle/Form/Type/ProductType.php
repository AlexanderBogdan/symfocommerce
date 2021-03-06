<?php

namespace Eshop\ShopBundle\Form\Type;

use Eshop\ShopBundle\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('slug', TextType::class)
            ->add('description', TextareaType::class)
            ->add('type', EntityType::class, array(
                'required' => true,
                'multiple' => true,
                'class' => 'Eshop\ShopBundle\Entity\ProductType',
                'choice_label' => 'name',
            ))
            ->add('price', NumberType::class)
            ->add('oldPrice', NumberType::class)
            ->add('category', null, array(
                'required' => false,
                'multiple' => true,
                'class' => 'Eshop\ShopBundle\Entity\Category',
                'choice_label' => 'name',
            ))
            ->add('manufacturer', EntityType::class, array(
                'required' => true,
                'multiple' => false,
                'class' => 'Eshop\ShopBundle\Entity\Manufacturer',
                'choice_label' => 'name',
            ))
            ->add('quantity', IntegerType::class)
            ->add('metaKeys', TextType::class)
            ->add('metaDescription', TextType::class)
            ->add('material', ChoiceType::class, array(
                'required' => false,
                'multiple' => true,
                'choices' => [
                    'Дерево' => 'Wood',
                    'Пластик' => 'Plastic',
                    'Резина' => 'Rubber',
                    'Металл' => 'Metal',
                ],
            ))
            ->add('male', ChoiceType::class, array(
                'required' => false,
                'choices' => [
                    'Мальчик' => 'm',
                    'Девочка' => 'f',
                ],
            ))
            ->add('ageFrom', ChoiceType::class, array(
                'required' => false,
                'choices' => [
                    '3 месяца' => '3',
                    '6 месяцев' => '6',
                    '9 месяцев' => '9',
                    '1 год' => '12',
                    '1.5 года' => '18',
                    '2 года' => '24',
                    '3 года' => '36',
                    '4 года' => '48',
                    '5 лет' => '60',
                    '6 лет' => '72',
                    '7 лет' => '84',
                    '8 лет' => '96',
                    '9 лет' => '108',
                    '10 лет' => '120',
                    '11 лет' => '132',
                    '12 лет' => '144',
                    '13 лет' => '156',
                    '14 лет' => '168',
                    '15 лет' => '180',
                    '16 лет' => '192',
                ],
                )
            )
            ->add('ageTo', ChoiceType::class, array(
                'required' => false,
                'choices' => [
                    NULL => '',
                    '3 месяца' => '3',
                    '6 месяцев' => '6',
                    '9 месяцев' => '9',
                    '1 год' => '12',
                    '1.5 года' => '18',
                    '2 года' => '24',
                    '3 года' => '36',
                    '4 года' => '48',
                    '5 лет' => '60',
                    '6 лет' => '72',
                    '7 лет' => '84',
                    '8 лет' => '96',
                    '9 лет' => '108',
                    '10 лет' => '120',
                    '11 лет' => '132',
                    '12 лет' => '144',
                    '13 лет' => '156',
                    '14 лет' => '168',
                    '15 лет' => '180',
                    '16 лет' => '192',
                ],
            ))
            ->add('countryManufacturer', CountryType::class, array(
                'required' => false,
            ))
            ->add('isNovelty', CheckboxType::class, array(
                'required' => false,
                )
            )
            ->add('purpose', ChoiceType::class, array(
                'required' => false,
                'choices' => [
                    'Развитие мелкой моторики' =>'Развитие мелкой моторики',
                    'Первые слова' => 'Первые слова',
                    'Развитие координации' => 'Развитие координации',
                    'Развитие воображения' => 'Развитие воображения',
                    'Физическое развитие' => 'Физическое развитие',
                    'Развитие органов чувств' => 'Развитие органов чувств',
                    'Развитие мышления' => 'Развитие мышления',
                ],
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eshop\ShopBundle\Entity\Product'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'eshop_shopbundle_product';
    }
}
