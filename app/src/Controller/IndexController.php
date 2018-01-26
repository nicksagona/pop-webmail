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
 * Index controller class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.0.1-alpha
 */
class IndexController extends AbstractController
{

    /**
     * Index action method
     *
     * @return void
     */
    public function index()
    {
        $this->redirect('/mail');
    }

    /**
     * Login action method
     *
     * @return void
     */
    public function login()
    {
        if (isset($this->application->services['session']->user)) {
            $this->redirect('/mail');
        }

        $this->prepareView('login.phtml');
        $this->view->title = 'Please Login';
        $this->view->form  = Form\Login::createFromFieldsetConfig(
            $this->application->config()['forms']['PopWebmail\Form\Login']
        );

        if ($this->request->isPost()) {
            $this->view->form->addFilter('strip_tags')
                ->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8', false])
                ->setFieldValues($this->request->getPost());

            $user = new Model\User();

            if ($user->authenticate($this->view->form->username, $this->view->form->password)) {
                $user->login(
                    $user->id,
                    $this->application->services['session'],
                    $this->application->services['cookie']
                );
                $this->redirect('/mail');
            } else {
                $this->view->error = 'Login Failed.';
            }
        }

        $this->send();
    }

    /**
     * Logout action method
     *
     * @return void
     */
    public function logout()
    {
        $user = new Model\User();
        $user->logout(
            $this->application->services['session'],
            $this->application->services['cookie']
        );
        $this->redirect('/login');
    }

    /**
     * Error action method
     *
     * @return void
     */
    public function error()
    {
        $this->prepareView('error.phtml');
        $this->view->title = 'Error';
        $this->send();
    }

}