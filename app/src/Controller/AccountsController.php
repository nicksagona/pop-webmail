<?php
/**
 * Pop Webmail Application
 *
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace PopWebmail\Controller;

use PopWebmail\Form;
use PopWebmail\Model;

/**
 * Accounts controller class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.0.1-alpha
 */
class AccountsController extends AbstractController
{

    /**
     * Index action method
     *
     * @return void
     */
    public function index()
    {
        $this->prepareView('mail/accounts/index.phtml');
        $this->view->title = 'Accounts';
        $this->view->pages = null;
        $this->send();
    }



    /**
     * Create action method
     *
     * @return void
     */
    public function create()
    {
        $this->prepareView('mail/accounts/create.phtml');
        $this->view->title = 'Account : Add';

        $this->view->form = Form\Mail\Account::createFromFieldsetConfig($this->application->config['forms']['PopWebmail\Form\Mail\Account']);
        $this->view->form->addColumn([1, 2, 3], 'form-left-column')
            ->addColumn(4, 'form-right-column');

        if ($this->request->isPost()) {
            $this->view->form->addFilter('strip_tags')
                ->setFieldValues($this->request->getPost())
                ->addValidators();

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                    ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                    ->filterValues();

                $account = new Model\Account();
                $a       = $account->create($this->view->form->toArray());
                $this->application->services['session']->setRequestValue('saved', true);
                $this->redirect('/mail/accounts/' . $a['id']);
            }
        }

        $this->send();
    }


    /**
     * Update action method
     *
     * @param  int $id
     * @return void
     */
    public function update($id)
    {
        $user = (new Model\Account())->getById($id);
        $this->prepareView('mail/accounts/update.phtml');
        $this->view->title = 'Users : ' . $user['username'];

        if (null !== $this->request->getQuery('role_id')) {
            $this->view->roleId = (int)$this->request->getQuery('role_id');
        }

        $roles  = (new Users\Model\Role())->getAll(null, null);
        $fields = $this->application->config['forms']['Pab\Http\Web\Form\User'];
        foreach ($roles as $role) {
            $fields[3]['role_ids']['values'][$role['id']] = $role['name'];
        }

        $this->view->form = Form\User::createFromFieldsetConfig($fields);
        $this->view->form->addColumn([1, 2, 3], 'form-left-column')
            ->addColumn(4, 'form-right-column');

        if ($this->request->isPost()) {
            $this->view->form->addFilter('strip_tags')
                ->setFieldValues($this->request->getPost())
                ->addValidators();

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                    ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                    ->filterValues();

                $user = new Model\Account();
                $user->update($this->view->form->toArray());
                $this->application->services['session']->setRequestValue('saved', true);
                $this->redirect('/users/' . $id);
            }
        } else {
            $this->view->form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8', false])
                ->setFieldValues($user);
        }

        $this->send();
    }


    /**
     * Delete action method
     *
     * @return void
     */
    public function delete()
    {
        if ($this->request->isPost() && !empty($this->request->getPost('rm_users'))) {
            $user = new Model\Account();
            $user->delete($this->request->getPost('rm_users'));
            $this->application->services['session']->setRequestValue('removed', true);
            $this->redirect(
                '/users' . ((null !== $this->request->getQuery('role_id')) ? '?role_id=' . (int)$this->request->getQuery('role_id') : null)
            );
        }
    }
    
}