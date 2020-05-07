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

namespace Zikula\ContentModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Form\DataTransformer\PageTransformer;
use Zikula\ContentModule\Form\Type\Field\EntityTreeType;

/**
 * Content item moving and copying form type implementation class.
 */
class MoveCopyContentItemType extends AbstractType
{
    /**
     * @var EntityFactory
     */
    private $entityFactory;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('destinationPage', EntityTreeType::class, [
            'class' => PageEntity::class,
            'multiple' => false,
            'expanded' => false,
            'use_joins' => false,
            'label' => 'Destination page:'
        ]);
        $transformer = new PageTransformer($this->entityFactory);
        $builder->get('destinationPage')->addModelTransformer($transformer);

        $builder->add('operationType', ChoiceType::class, [
            'label' => 'Operation type:',
            'label_attr' => [
                'class' => 'radio-custom'
            ],
            'empty_data' => 'copy',
            'choices' => [
                'Move' => 'move',
                'Copy' => 'copy'
            ],
            'multiple' => false,
            'expanded' => true
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_movecopycontentitem';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'contentItem'
        ]);
    }
}