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
use Pop\Dir\Dir;
use Pop\Http\Upload;
use Pop\Mail\Message;
use Pop\Paginator;

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
        $page    = (null !== $this->request->getQuery('page')) ? (int)$this->request->getQuery('page') : 1;
        $limit   = $this->application->config['pagination'];
        $search  = [];

        foreach ($this->request->getQuery() as $key => $value) {
            if ((substr($key, 0, 7) == 'search_') && ($value !== '----') && !empty($value)) {
                $search[strtoupper(substr($key, 7))] = $value;
            }
        }

        if (null !== $this->request->getQuery('sort')) {
            $sort = $this->request->getQuery('sort');
            if (substr($sort, 0, 1) == '-') {
                $sort    = substr($sort, 1);
                $reverse = true;
            } else {
                $reverse = false;
            }
        } else {
            $sort    = SORTDATE;
            $reverse = true;
        }

        $this->prepareView('mail/index.phtml');
        $this->view->accounts = (new Model\Account())->getAll();

        if (!isset($this->application->services['session']->currentAccountId)) {
            foreach ($this->view->accounts as $account) {
                if ($account['default']) {
                    $this->application->services['session']->currentAccountId   = $account['id'];
                    $this->application->services['session']->currentAccountName = $account['name'];
                    break;
                }
            }
        }

        $mail = new Model\Mail();
        $mail->loadAccount($this->application->services['session']->currentAccountId);

        if ($mail->isImapLoaded()) {
            if (null !== $this->request->getQuery('folder')) {
                $currentFolder = $this->request->getQuery('folder');
                $mail->setFolder($currentFolder)->open('/ssl');
            } else {
                $currentFolder = 'INBOX';
            }
            $this->application->services['session']->currentFolder = $currentFolder;
            $this->view->currentAccountId   = $this->application->services['session']->currentAccountId;
            $this->view->currentAccountName = $this->application->services['session']->currentAccountName;

            if (!isset($this->application->services['session']->imapFolders)) {
                $this->application->services['session']->imapFolders = $mail->getFolders();
                $this->application->services['session']->mailboxes   = $mail->getMailboxes();
            }
            $this->view->imapFolders   = $this->application->services['session']->imapFolders;
            $this->view->currentFolder = $currentFolder;
            $this->view->title         = $currentFolder;
            $this->view->messages      = $mail->fetchAll($page, $limit, $sort, $reverse, $search);
            $this->view->mailboxTotal  = $mail->getMailboxTotal();
            $this->view->unread        = $mail->getNumberOfUnread();
            $this->view->mailboxes     = $mail->getMailboxes();
            $this->view->pages         = ($mail->hasPages($limit)) ?
                new Paginator\Form($mail->getMailboxTotal(), $limit) : null;
        } else {
            $this->view->title = 'Mail';
            $this->view->pages = null;
        }

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
        $account = (new Model\Account())->getById($id);
        $this->application->services['session']->currentAccountId   = $id;
        $this->application->services['session']->currentAccountName = $account['name'];
        unset($this->application->services['session']->imapFolders);
        $this->redirect('/mail');
    }

    /**
     * Process action method
     *
     * @return void
     */
    public function process()
    {
        if ($this->request->isPost()) {
            $mail = new Model\Mail();
            $mail->loadAccount($this->application->services['session']->currentAccountId);
            if (isset($this->application->services['session']->currentFolder)) {
                $mail->setFolder($this->application->services['session']->currentFolder)->open('/ssl');
            }
            $mail->process($this->request->getPost(), $this->application->services['session']->mailboxes);
            if ($this->request->getPost('mail_process_action') == -1) {
                $this->application->services['session']->setRequestValue('removed', true);
            } else {
                $this->application->services['session']->setRequestValue('saved', true);
            }
            $this->redirect('/mail?folder=' . $this->request->getPost('folder'));
        } else {
            $this->redirect('/mail');
        }

    }

    /**
     * Compose action method
     *
     * @return void
     */
    public function compose()
    {
        $mail = new Model\Mail();
        $mail->loadAccount($this->application->services['session']->currentAccountId);

        $this->prepareView('mail/compose.phtml');
        $this->view->title  = 'Compose Message';
        $this->view->folder = uniqid();
        $this->view->form   = Form\Mail\Compose::createFromFieldsetConfig(
            $this->application->config['forms']['PopWebmail\Form\Mail\Compose']
        );
        $this->view->form->setFieldValue('folder', $this->view->folder);

        if (null !== $this->request->getQuery('to')) {
            $this->view->form->setFieldValue('to', $this->request->getQuery('to'));
        }

        if (null !== $this->request->getQuery('id')) {
            if (isset($this->application->services['session']->currentFolder)) {
                $mail->setFolder($this->application->services['session']->currentFolder)->open('/ssl');
            }

            $this->view->id = $this->request->getQuery('id');

            if (null !== $this->request->getQuery('action')) {
                $message     = $mail->fetchById($this->request->getQuery('id'));
                $subject     = $mail->decodeText($message->headers->subject);
                $toAddresses = [];
                $ccAddresses = [];

                if (!empty($message->headers->from)) {
                    foreach ($message->headers->from as $from) {
                        $toAddresses[] = $from->mailbox . '@' . $from->host;
                    }
                }
                if (!empty($message->headers->cc)) {
                    foreach ($message->headers->cc as $cc) {
                        $ccAddresses[] = $cc->mailbox . '@' . $cc->host;
                    }
                }

                if (($this->request->getQuery('action') == 'reply') && isset($toAddresses[0])) {
                    $this->view->form->setFieldValue('subject', 'RE: ' . $subject);
                    $this->view->form->setFieldValue('to', $toAddresses[0]);
                } else if (($this->request->getQuery('action') == 'reply_all') && (count($toAddresses) > 0)) {
                    $this->view->form->setFieldValue('subject', 'RE: ' . $subject);
                    $this->view->form->setFieldValue('to', implode(', ', $toAddresses));
                    if (count($ccAddresses) > 0) {
                        $this->view->form->setFieldValue('cc', implode(', ', $ccAddresses));
                    }
                } else if ($this->request->getQuery('action') == 'forward') {
                    $this->view->form->setFieldValue('subject', 'FWD: ' . $subject);
                }

                $this->view->form->setFieldValue('message', PHP_EOL . PHP_EOL .
                    '----------------------------' . PHP_EOL . strip_tags(str_replace(['<br>', '<br />'], [PHP_EOL, PHP_EOL], $mail->getContent($message->parts)))
                );
            }
        }

        if ($this->request->isPost()) {
            $message = new Message($this->request->getPost('subject'));
            $message->setTo($this->request->getPost('to'));
            $message->setFrom($mail->imap()->getUsername());

            if (null !== $this->request->getPost('cc')) {
                $message->setCc($this->request->getPost('cc'));
            }

            if (null !== $this->request->getPost('bcc')) {
                $message->setBcc($this->request->getPost('bcc'));
            }

            if (!empty($this->request->getPost('folder')) &&
                file_exists(__DIR__ . '/../../../data/tmp/' . $this->request->getPost('folder'))) {
                $folder = $this->request->getPost('folder');
                $dir    = new Dir(__DIR__ . '/../../../data/tmp/' . $folder, ['filesOnly' => true]);

                foreach ($dir as $file) {
                    if (!empty($file) && file_exists(__DIR__ . '/../../../data/tmp/' . $folder . '/' . $file)) {
                        $message->attachFile(__DIR__ . '/../../../data/tmp/' . $folder . '/' . $file);
                    }
                }

                $dir->emptyDir(true);
            }

            $message->setBody($this->request->getPost('message'));
            $mail->mailer()->send($message);

            $this->view->sent = true;
        }

        $this->send();
    }

    /**
     * View action method
     *
     * @param  int $id
     * @return void
     */
    public function view($id)
    {
        $mail = new Model\Mail();
        $mail->loadAccount($this->application->services['session']->currentAccountId);

        if (isset($this->application->services['session']->currentFolder)) {
            $mail->setFolder($this->application->services['session']->currentFolder)->open('/ssl');
        }

        $this->prepareView('mail/view.phtml');

        $this->view->id          = $id;
        $this->view->message     = $mail->fetchById($id);
        $this->view->content     = $mail->getContent($this->view->message->parts);
        $this->view->attachments = $mail->getAttachmentLinks($this->view->message->parts, $id);
        $this->view->title       = (isset($this->view->message) && isset($this->view->message->headers) && isset($this->view->message->headers->Subject)) ?
            $mail->decodeText($this->view->message->headers->Subject) : '';

        $this->send();
    }

    /**
     * Attachment action method
     *
     * @param  int $id
     * @param  int $i
     * @return void
     */
    public function attachments($id, $i)
    {
        $mail = new Model\Mail();
        $mail->loadAccount($this->application->services['session']->currentAccountId);

        if (isset($this->application->services['session']->currentFolder)) {
            $mail->setFolder($this->application->services['session']->currentFolder)->open('/ssl');
        }

        $attachment = $mail->fetchAttachment($id, $i - 1);

        $this->response->setHeader('Content-Type', $attachment->type)
            ->setHeader('Content-Disposition', $attachment->disposition . 'filename="' . $attachment->filename . '"')
            ->setBody($attachment->content);

        $this->response->send();
    }

    /**
     * Upload action method
     *
     * @return void
     */
    public function upload()
    {
        $folder = $this->request->getPost('folder');
        $json   = [
            'folder' => $folder,
            'files'  => []
        ];

        if (!file_exists(__DIR__ . '/../../../data/tmp/' . $folder)) {
            mkdir(__DIR__ . '/../../../data/tmp/' . $folder);
            chmod(__DIR__ . '/../../../data/tmp/' . $folder, 0777);
        }

        foreach ($_FILES as $key => $file) {
            $upload   = new Upload(__DIR__ . '/../../../data/tmp/' . $folder);
            $filename = $upload->upload($file);
            $json['files'][] = $filename;
        }

        $this->send(200, json_encode($json, JSON_PRETTY_PRINT));
    }

    /**
     * Clean up action method
     *
     * @return void
     */
    public function clean()
    {
        $folder = $this->request->getPost('folder');
        if (!empty($folder) && file_exists(__DIR__ . '/../../../data/tmp/' . $folder)) {
            $dir = new Dir(__DIR__ . '/../../../data/tmp/' . $folder);
            $dir->emptyDir(true);
        }
    }

    /**
     * Add folder method
     *
     * @return void
     */
    public function addFolder()
    {
        if ($this->request->isPost()) {
            $mail = new Model\Mail();
            $mail->loadAccount($this->application->services['session']->currentAccountId);

            if (isset($this->application->services['session']->currentFolder)) {
                $mail->setFolder($this->application->services['session']->currentFolder)->open('/ssl');
            }

            $mail->addFolder($this->request->getPost());
            $this->application->services['session']->setRequestValue('saved', true);
            unset($this->application->services['session']->currentFolder);
            unset($this->application->services['session']->imapFolders);
            unset($this->application->services['session']->mailboxes);
        }

        $this->redirect('/mail');
    }

    /**
     * Rename folder method
     *
     * @return void
     */
    public function renameFolder()
    {
        if ($this->request->isPost()) {
            $mail = new Model\Mail();
            $mail->loadAccount($this->application->services['session']->currentAccountId);

            if (isset($this->application->services['session']->currentFolder)) {
                $mail->setFolder($this->application->services['session']->currentFolder)->open('/ssl');
            }

            $mail->renameFolder($this->request->getPost());
            $this->application->services['session']->setRequestValue('saved', true);
            unset($this->application->services['session']->currentFolder);
            unset($this->application->services['session']->imapFolders);
            unset($this->application->services['session']->mailboxes);
        }

        $this->redirect('/mail');
    }

    /**
     * Remove folder method
     *
     * @return void
     */
    public function removeFolder()
    {
        if ($this->request->isPost()) {
            $mail = new Model\Mail();
            $mail->loadAccount($this->application->services['session']->currentAccountId);

            if (isset($this->application->services['session']->currentFolder)) {
                $mail->setFolder($this->application->services['session']->currentFolder)->open('/ssl');
            }

            $mail->removeFolder($this->request->getPost());
            $this->application->services['session']->setRequestValue('removed', true);
            unset($this->application->services['session']->currentFolder);
            unset($this->application->services['session']->imapFolders);
            unset($this->application->services['session']->mailboxes);
        }

        $this->redirect('/mail');
    }

}