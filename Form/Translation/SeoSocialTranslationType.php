<?php

namespace PN\SeoBundle\Form\Translation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class SeoSocialTranslationType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title', TextType::class, ["attr" => ["class" => "countLength", "data-max-length" => "60"]])
                ->add('description', TextareaType::class, ["attr" => ["class" => "countLength", "data-preview" => "snippetPreviewMetaDescription", "data-max-length" => "300"]])
                ->add('imageUrl', UrlType::class)

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => \PN\SeoBundle\Entity\Translation\SeoSocialTranslation::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'pn_bundle_seobundle_seo';
    }

}
