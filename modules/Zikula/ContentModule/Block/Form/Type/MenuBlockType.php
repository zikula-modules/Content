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

namespace Zikula\ContentModule\Block\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Form\DataTransformer\PageTransformer;
use Zikula\ContentModule\Form\Type\Field\EntityTreeType;

/**
 * Menu block form type implementation class.
 */
class MenuBlockType extends AbstractType
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('navType', ChoiceType::class, [
            'label' => 'Navigation type:',
            'choices' => [
                'None' => '0',
                'Tabs' => '1',
                'Pills' => '2',
                'Navbar' => '3'
            ]
        ]);
        $builder->add('subPagesHandling', ChoiceType::class, [
            'label' => 'Sub pages handling:',
            'choices' => [
                'Hide them' => 'hide',
                'Use dropdowns' => 'dropdown'
            ]
        ]);
        $builder->add('root', EntityTreeType::class, [
            'class' => PageEntity::class,
            'multiple' => false,
            'expanded' => false,
            'use_joins' => false,
            'placeholder' => 'All pages',
            'required' => false,
            'label' => 'Include the following subpages:'
        ]);
        $transformer = new PageTransformer($this->entityFactory);
        $builder->get('root')->addModelTransformer($transformer);
        $helpText = 'The maximum amount of items to be shown.'
            . ' '
            . 'Only digits are allowed.'
        ;
        $builder->add('amount', IntegerType::class, [
            'label' => 'Amount:',
            'attr' => [
                'maxlength' => 2,
                'title' => $helpText
            ],
            'help' => $helpText,
            'empty_data' => 5
        ]);
        $builder->add('inMenu', CheckboxType::class, [
            'label' => 'Use only pages activated for the menu:',
            'label_attr' => ['class' => 'switch-custom'],
            'required' => false
        ]);
        $builder->add('filter', TextType::class, [
            'label' => 'Filter (expert option):',
            'required' => false,
            'attr' => [
                'maxlength' => 255,
                'title' => 'Example: tbl.age >= 18'
            ],
            'help' => 'Example: <code>tbl.age >= 18</code>',
            'help_html' => true
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_menublock';
    }
}
