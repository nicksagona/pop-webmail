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
use Pop\Paginator;

/**
 * Accounts controller class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.9-beta
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
        $account = new Model\Account();
        $page    = (null !== $this->request->getQuery('page')) ? (int)$this->request->getQuery('page') : 1;
        $limit   = $this->application->config['pagination'];
        $sort    = (null !== $this->request->getQuery('sort')) ? $this->request->getQuery('sort') : null;

        $this->prepareView('mail/accounts/index.phtml');
        $this->view->title    = 'Accounts';
        $this->view->accounts = $account->getAll($page, $limit, $sort);
        $this->view->pages    = ($account->hasPages($limit)) ? new Paginator\Form($account->getCount(), $limit) : null;
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
        $this->view->title = 'Accounts : Add';

        $this->view->form = Form\Mail\Account::createFromFieldsetConfig(
            $this->application->config['forms']['PopWebmail\Form\Mail\Account']
        );
        $this->view->form->addColumn([1, 2, 3, 4], 'form-left-column')
            ->addColumn(5, 'form-right-column');

        if ($this->request->isPost()) {
            $this->view->form->setFieldValues($this->request->getPost());

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
        $account = (new Model\Account())->getById($id);
        $this->prepareView('mail/accounts/update.phtml');
        $this->view->title = 'Accounts : ' . $account['name'];

        $this->view->form = Form\Mail\Account::createFromFieldsetConfig(
            $this->application->config['forms']['PopWebmail\Form\Mail\Account']
        );
        $this->view->form->addColumn([1, 2, 3, 4], 'form-left-column')
            ->addColumn(5, 'form-right-column');

        if ($this->request->isPost()) {
            $this->view->form->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                    ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                    ->filterValues();

                $account = new Model\Account();
                $account->update($this->view->form->toArray());
                $this->application->services['session']->setRequestValue('saved', true);
                $this->redirect('/mail/accounts/' . $id);
            }
        } else {
            $this->view->form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8', false])
                ->setFieldValues($account);
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
        if ($this->request->isPost() && !empty($this->request->getPost('rm_accounts'))) {
            $account = new Model\Account();
            $account->delete($this->request->getPost('rm_accounts'));
            $this->application->services['session']->setRequestValue('removed', true);
        }
        $this->redirect('/mail/accounts');
    }

}