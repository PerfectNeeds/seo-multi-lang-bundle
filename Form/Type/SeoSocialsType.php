<?php

namespace PN\SeoBundle\Form\Type;

use PN\SeoBundle\Entity\SeoSocial;
use PN\SeoBundle\Form\SeoSocialType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoSocialsType extends AbstractType
{

    /**
     * @var array
     */
    private $forDelete = [];
    private $multipleLanguages = true;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->multipleLanguages = $options['multipleLanguages'];
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'))
            ->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'))
            ->addEventListener(FormEvents::SUBMIT, array($this, 'submit'));
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();

        $socialNetworks = SeoSocial::$socialNetworks;
        foreach ($socialNetworks as $socialNetwork) {
            if (!$form->has($socialNetwork['type'])) {
                $form->add($socialNetwork['type'], SeoSocialType::class, $this->getOptions($socialNetwork['type']));
            }
        }
        $dataSocialNetworks = $event->getData();
        $newData = [];
        if ($dataSocialNetworks instanceof \Doctrine\ORM\PersistentCollection) {
            foreach ($dataSocialNetworks as $dataSocialNetwork) {
                $newData[$dataSocialNetwork->getSocialNetwork()] = $dataSocialNetwork;
            }
        }
        $event->setData($newData);
    }

    private function getOptions($seoSocialType)
    {
        $options['required'] = false;
        $options['label'] = false;


        $options['property_path'] = '['.$seoSocialType.']';
        $options['empty_data'] = function (FormInterface $form) {
            if ($form->isEmpty()) {
                return null;
            } else {
                $dataClass = $form->getConfig()->getOption('data_class');

                return new $dataClass;
            }
        };
        $options['error_bubbling'] = false;
        $options['multipleLanguages'] = $this->multipleLanguages;

        return $options;
    }

    public function onPreSubmit(FormEvent $event)
    {
        $dataSocialNetworks = $event->getData();

        $isEmpty = function ($data) use (&$isEmpty) {
            if (is_array($data)) {
                foreach ($data as $each) {
                    if (!$isEmpty($each)) {
                        return false;
                    }
                }

                return true;
            }

            return empty($data);
        };

        $socialNetworks = SeoSocial::$socialNetworks;
        foreach ($socialNetworks as $socialNetwork) {
            $socialNetworkType = $socialNetwork['type'];
            if (isset($dataSocialNetworks[$socialNetworkType]) && $isEmpty($dataSocialNetworks[$socialNetworkType])) {
                $this->forDelete[$socialNetworkType] = $socialNetworkType;
            }
        }
    }

    public function submit(FormEvent $event)
    {
        $dataSocialNetworks = $event->getData();
        $forDelete = [];
        $socialNetworks = SeoSocial::$socialNetworks;

        foreach ($socialNetworks as $socialNetwork) {
            $socialNetworkType = $socialNetwork['type'];
            $dataSocialNetwork = $dataSocialNetworks[$socialNetworkType];
            if ($dataSocialNetwork === null) {
                $forDelete[] = $socialNetworkType;
            } else {
                $dataSocialNetwork = $dataSocialNetworks[$socialNetworkType];
                if (method_exists($dataSocialNetwork, "setSocialNetwork")) {
                    $dataSocialNetwork->setSocialNetwork($socialNetworkType);
                }
            }
        }
        foreach ($forDelete as $item) {
            if (array_key_exists($item, $dataSocialNetworks)) {
                unset($dataSocialNetworks[$item]);
            }
        }
        foreach ($this->forDelete as $item) {
            if (array_key_exists($item, $dataSocialNetworks)) {
                unset($dataSocialNetworks[$item]);
            }
        }
        $event->setData($dataSocialNetworks);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'by_reference' => false,
            "multipleLanguages" => true,
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'SeoSocialNetworkType';
    }

}
