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

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translation\Extractor\Annotation\Ignore;
use Translation\Extractor\Annotation\Translate;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentFormType;

/**
 * Computer code form type class.
 */
class ComputerCodeType extends AbstractContentFormType
{
    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;

    public function __construct(ZikulaHttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $filterChoices = [
            /** @Translate */'Use native filter' => 'native'
        ];
        if ($this->kernel->isBundle('ZikulaBBCodeModule')) {
            $filterChoices[/** @Translate */'Use BBCode filter'] = 'bbcode';
        }
        if ($this->kernel->isBundle('PhaidonLuMicuLaModule')) {
            $filterChoices[/** @Translate */'Use LuMicuLa filter'] = 'lumicula';
        }

        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Computer code lines:'
            ])
            ->add('codeFilter', ChoiceType::class, [
                'label' => 'Code filter:',
                'help' => 'If ZikulaBBCodeModule or PhaidonLuMicuLaModule are available, you can filter your code with them instead of the native filter. There is no need to hook these modules to Content for this functionality.',
                /** @Ignore */
                'choices' => $filterChoices,
                'expanded' => true
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_computercode';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'translation_domain' => 'contentTypes'
        ]);
    }
}
