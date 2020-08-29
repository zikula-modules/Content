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

namespace Zikula\ContentModule\Form\EventListener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Translation listener base class.
 *
 * Based on https://github.com/a2lix/TranslationFormBundle/blob/master/src/Form/EventListener/TranslationsListener.php
 */
abstract class AbstractTranslationListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }
    
    /**
     * Adds translation fields to the form.
     */
    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $formOptions = $form->getConfig()->getOptions();
    
        $entityForm = $this->getEntityForm($form->getParent());
    
        foreach ($formOptions['fields'] as $fieldName) {
            if (!$entityForm->has($fieldName)) {
                continue;
            }
    
            $originalFieldConfig = $entityForm->get($fieldName)->getConfig();
            $fieldOptions = $originalFieldConfig->getOptions();
            $fieldOptions['required'] = $fieldOptions['required']
                && in_array($fieldName, $formOptions['mandatory_fields'], true)
            ;
            $fieldOptions['data'] = $formOptions['values'][$fieldName] ?? null;
    
            $form->add($fieldName, get_class($originalFieldConfig->getType()->getInnerType()), $fieldOptions);
        }
    }
    
    /**
     * Returns parent form editing the entity.
     */
    protected function getEntityForm(FormInterface $form): FormInterface
    {
        $parentForm = $form;
        do {
            $parentForm = $form;
        } while ($form->getConfig()->getInheritData() && ($form = $form->getParent()));
    
        return $parentForm;
    }
}
