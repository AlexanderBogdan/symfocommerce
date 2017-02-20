<?php

namespace Eshop\ShopBundle\Form\Type;

use Eshop\ShopBundle\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('phone', TextType::class)
            ->add('address', TextType::class)
            ->add('comment', TextareaType::class, array(
                'required' => false
            ))
            ->add('deliveryType', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'choices' => [
                    'Самовывоз' => Orders::DELIVERY_TYPE_SELF_PICKUP,
                    'Новая почта' => Orders::DELIVERY_TYPE_NOVA_POSHTA,
                ],
            ])
            ->add('paymentType', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'choices' => [
                    'Наложенным платежом' => Orders::PAYMENT_TYPE_CASH_ON_DELIVERY,
                    'Наличными курьеру' => Orders::PAYMENT_TYPE_CASH_COURIERS,
                    'Банковской картой' => Orders::PAYMENT_TYPE_CREDIT_CARD,
                    'Оплатить по счёту' => Orders::PAYMENT_TYPE_PAY_ON_ACCOUNT,
                ],
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'choices' => [
                    'Новый' => Orders::ORDER_STATUS_NEW,
                    'В обработке' => Orders::ORDER_STATUS_IN_PROGRESS,
                    'Отправлен (отправлено по НП)' => Orders::ORDER_STATUS_SEND,
                    'Завершен (получены деньги)' => Orders::ORDER_STATUS_RECEIVED,
                ],
            ])
            ->add('city', TextType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eshop\ShopBundle\Entity\Orders'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'eshop_shopbundle_orders';
    }
}
