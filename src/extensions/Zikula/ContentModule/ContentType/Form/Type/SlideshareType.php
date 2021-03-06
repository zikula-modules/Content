<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\ExtensionsModule\ModuleInterface\Content\Form\Type\AbstractContentFormType;

/**
 * Slideshare form type class.
 */
class SlideshareType extends AbstractContentFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, [
                'label' => 'URL to the slide:',
                'help' => 'Something like "https://www.slideshare.net/1Marc/jquery-essentials".',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Slide description:',
                'required' => false,
            ])
            ->add('width', IntegerType::class, [
                'label' => 'Slideshare\'s embedded player width:',
                'input_group' => [
                    'right' => 'pixels',
                ],
            ])
            ->add('height', IntegerType::class, [
                'label' => 'Slideshare\'s embedded player height:',
                'input_group' => [
                    'right' => 'pixels',
                ],
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_slideshare';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'translation_domain' => 'contentTypes',
        ]);
    }
}
