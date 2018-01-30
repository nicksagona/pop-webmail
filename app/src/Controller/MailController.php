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

use PopWebmail\Model;

/**
 * Mail controller class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.0.1-alpha
 */
class MailController extends AbstractController
{

    /**
     * Index action method
     *
     * @return void
     */
    public function index()
    {
        $this->prepareView('mail/index.phtml');
        $this->view->title    = 'Mail';
        $this->view->pages    = null;
        $this->view->accounts = (new Model\Account())->getAll();

        if (!isset($this->application->services['session']->currentAccountId)) {
            foreach ($this->view->accounts as $account) {
                if ($account['default']) {
                    $this->application->services['session']->currentAccountId = $account['id'];
                    break;
                }
            }
        }

        $this->view->currentAccountId = $this->application->services['session']->currentAccountId;

        $this->send();
    }

    /**
     * Box action method
     *
     * @param  int $id
     * @return void
     */
    public function box($id)
    {
        $this->application->services['session']->currentAccountId = $id;
        $this->redirect('/mail');
    }

}