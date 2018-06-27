<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Form\Handler\ContentItem\Base;

use Zikula\ContentModule\Form\Handler\Common\EditHandler;
use Zikula\ContentModule\Form\Type\ContentItemType;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use RuntimeException;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the content item object type.
 */
abstract class AbstractEditHandler extends EditHandler
{
    /**
     * Initialise form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param array $templateParameters List of preassigned template variables
     *
     * @return boolean False in case of initialisation errors, otherwise true
     */
    public function processForm(array $templateParameters = [])
    {
        $this->objectType = 'contentItem';
        $this->objectTypeCapital = 'ContentItem';
        $this->objectTypeLower = 'contentitem';
        
        $this->hasPageLockSupport = true;
        $this->hasTranslatableFields = true;
    
        $result = parent::processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
    
        if ($this->templateParameters['mode'] == 'create') {
            if (!$this->modelHelper->canBeCreated($this->objectType)) {
                $this->request->getSession()->getFlashBag()->add('error', $this->__('Sorry, but you can not create the content item yet as other items are required which must be created before!'));
                $logArgs = ['app' => 'ZikulaContentModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $this->objectType];
                $this->logger->notice('{app}: User {user} tried to create a new {entity}, but failed as it other items are required which must be created before.', $logArgs);
    
                return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
            }
        }
    
        $entityData = $this->entityRef->toArray();
    
        // assign data to template as array (for additions like standard fields)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
    
        return $result;
    }
    
    /**
     * Initialises relationship presets.
     */
    protected function initRelationPresets()
    {
        $entity = $this->entityRef;
    
        
        // assign identifiers of predefined incoming relationships
        // non-editable relation, we store the id and assign it in handleCommand
        $this->relationPresets['page'] = $this->request->get('page', '');
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    }
    
    /**
     * Creates the form type.
     */
    protected function createForm()
    {
        return $this->formFactory->create(ContentItemType::class, $this->entityRef, $this->getFormOptions());
    }
    
    /**
     * Returns the form options.
     *
     * @return array
     */
    protected function getFormOptions()
    {
        $options = [
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'has_moderate_permission' => $this->permissionApi->hasPermission($this->permissionComponent, $this->idValue . '::', ACCESS_ADMIN),
            'filter_by_ownership' => !$this->permissionApi->hasPermission($this->permissionComponent, $this->idValue . '::', ACCESS_ADD),
            'inline_usage' => $this->templateParameters['inlineUsage']
        ];
    
        $options['translations'] = [];
        foreach ($this->templateParameters['supportedLanguages'] as $language) {
            $options['translations'][$language] = isset($this->templateParameters[$this->objectTypeLower . $language]) ? $this->templateParameters[$this->objectTypeLower . $language] : [];
        }
    
        return $options;
    }


    /**
     * Get list of allowed redirect codes.
     *
     * @return string[] list of possible redirect codes
     */
    protected function getRedirectCodes()
    {
        $codes = parent::getRedirectCodes();
    
        // user index page of content item area
        $codes[] = 'userIndex';
        // admin index page of content item area
        $codes[] = 'adminIndex';
    
        // user list of pages
        $codes[] = 'userViewPages';
        // admin list of pages
        $codes[] = 'adminViewPages';
        // user list of own pages
        $codes[] = 'userOwnViewPages';
        // admin list of own pages
        $codes[] = 'adminOwnViewPages';
        // user detail page of related page
        $codes[] = 'userDisplayPage';
        // admin detail page of related page
        $codes[] = 'adminDisplayPage';
    
        return $codes;
    }

    /**
     * Get the default redirect url. Required if no returnTo parameter has been supplied.
     * This method is called in handleCommand so we know which command has been performed.
     *
     * @param array $args List of arguments
     *
     * @return string The default redirect url
     */
    protected function getDefaultReturnUrl(array $args = [])
    {
        $objectIsPersisted = $args['commandName'] != 'delete' && !($this->templateParameters['mode'] == 'create' && $args['commandName'] == 'cancel');
    
        if (null !== $this->returnTo) {
            $refererParts = explode('/', $this->returnTo);
            $isDisplayOrEditPage = $refererParts[count($refererParts)-1] == $this->idValue;
            if (!$isDisplayOrEditPage || $objectIsPersisted) {
                // return to referer
                return $this->returnTo;
            }
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters) ? $this->templateParameters['routeArea'] : '';
        $routePrefix = 'zikulacontentmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // redirect to the index page
        $url = $this->router->generate($routePrefix . 'index');
    
        return $url;
    }

    /**
     * Command event handler.
     *
     * This event handler is called when a command is issued by the user.
     *
     * @param array $args List of arguments
     *
     * @return mixed Redirect or false on errors
     */
    public function handleCommand(array $args = [])
    {
        $result = parent::handleCommand($args);
        if (false === $result) {
            return $result;
        }
    
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if ($this->templateParameters['mode'] == 'create' && $this->form->has('submitrepeat') && $this->form->get('submitrepeat')->isClicked()) {
            $args['commandName'] = 'submit';
            $this->repeatCreateAction = true;
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param array   $args    List of arguments from handleCommand method
     * @param boolean $success Becomes true if this is a success, false for default error
     *
     * @return String desired status or error message
     */
    protected function getDefaultMessage(array $args = [], $success = false)
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        $message = '';
        switch ($args['commandName']) {
            case 'submit':
                if ($this->templateParameters['mode'] == 'create') {
                    $message = $this->__('Done! Content item created.');
                } else {
                    $message = $this->__('Done! Content item updated.');
                }
                break;
            case 'delete':
                $message = $this->__('Done! Content item deleted.');
                break;
            default:
                $message = $this->__('Done! Content item updated.');
                break;
        }
    
        return $message;
    }

    /**
     * This method executes a certain workflow action.
     *
     * @param array $args List of arguments from handleCommand method
     *
     * @return boolean Whether everything worked well or not
     *
     * @throws RuntimeException Thrown if concurrent editing is recognised or another error occurs
     */
    public function applyAction(array $args = [])
    {
        // get treated entity reference from persisted member var
        $entity = $this->entityRef;
    
        $action = $args['commandName'];
    
        $success = false;
        $flashBag = $this->request->getSession()->getFlashBag();
        try {
            // execute the workflow action
            $success = $this->workflowHelper->executeAction($entity, $action);
        } catch (\Exception $exception) {
            $flashBag->add('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . ' ' . $exception->getMessage());
            $logArgs = ['app' => 'ZikulaContentModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => 'content item', 'id' => $entity->getKey(), 'errorMessage' => $exception->getMessage()];
            $this->logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed. Error details: {errorMessage}.', $logArgs);
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && $this->templateParameters['mode'] == 'create') {
            // store new identifier
            $this->idValue = $entity->getKey();
        }
        
        if ($args['commandName'] == 'create') {
            // save predefined incoming relationship from parent entity
            if (!empty($this->relationPresets['page'])) {
                $relObj = $this->entityFactory->getRepository('page')->selectBySlug($this->relationPresets['page']);
                if (null !== $relObj) {
                    $relObj->addContentItems($entity);
                }
            }
            $this->entityFactory->getObjectManager()->flush();
        }
    
        return $success;
    }

    /**
     * Get url to redirect to.
     *
     * @param array $args List of arguments
     *
     * @return string The redirect url
     */
    protected function getRedirectUrl(array $args = [])
    {
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        if ($this->request->getSession()->has('zikulacontentmodule' . $this->objectTypeCapital . 'Referer')) {
            $this->request->getSession()->remove('zikulacontentmodule' . $this->objectTypeCapital . 'Referer');
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes())) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        $routeArea = substr($this->returnTo, 0, 5) == 'admin' ? 'admin' : '';
        $routePrefix = 'zikulacontentmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'userIndex':
            case 'adminIndex':
                return $this->router->generate($routePrefix . 'index');
            case 'userViewPages':
            case 'adminViewPages':
                return $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'view');
            case 'userOwnViewPages':
            case 'adminOwnViewPages':
                return $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'view', ['own' => 1]);
            case 'userDisplayPage':
            case 'adminDisplayPage':
                if (!empty($this->relationPresets['page'])) {
                    return $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'display',  ['id' => $this->relationPresets['page']]);
                }
    
                return $this->getDefaultReturnUrl($args);
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
