<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\ExtensionsModule\ModuleInterface\Content\Form\Type\AbstractContentFormType;

/**
 * Breadcrumb form type class.
 */
class BreadcrumbType extends AbstractContentFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('includeSelf', CheckboxType::class, [
                'label' => 'Include self as last breadcrumb:',
                'label_attr' => ['class' => 'switch-custom'],
                'required' => false
            ])
            ->add('includeHome', CheckboxType::class, [
                'label' => 'Include home as first breadcrumb:',
                'label_attr' => ['class' => 'switch-custom'],
                'required' => false
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_breadcrumb';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'translation_domain' => 'contentTypes'
        ]);
    }
}