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
namespace PopWebmail\Model;

use Pop\Model\AbstractModel;
use Pop\Mail\Client\Imap;
use Pop\Mail\Mailer;
use Pop\Mail\Transport\Smtp;

/**
 * Mail model class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.0.1-alpha
 */
class Mail extends AbstractModel
{

    /**
     * IMAP resource for fetching mail
     *
     * @var Imap
     */
    protected $imap = null;

    /**
     * Mailer object for sending mail
     *
     * @var Mailer
     */
    protected $mailer = null;

    /**
     * Load account
     *
     * @param  int    $id
     * @param  string $folder
     * @param  void
     */
    public function loadAccount($id, $folder = '')
    {
        $account  = new Account();
        $settings = $account->getById($id);

        if (!empty($settings['id'])) {
            if (!empty($settings['imap_host']) && !empty($settings['imap_port']) &&
                !empty($settings['imap_username']) && !empty($settings['imap_password'])) {
                $this->imap = new Imap($settings['imap_host'], $settings['imap_port']);
                $this->imap->setUsername($settings['imap_username'])
                    ->setPassword($settings['imap_password'])
                    ->setFolder($folder)
                    ->open('/ssl');
            }

            if (!empty($settings['smtp_host']) && !empty($settings['smtp_port']) &&
                !empty($settings['smtp_username']) && !empty($settings['smtp_password'])) {
                $smtp = new Smtp($settings['smtp_host'], $settings['smtp_port']);
                $smtp->setUsername($settings['smtp_username'])
                    ->setPassword($settings['smtp_password']);

                if (!empty($settings['smtp_security'])) {
                    $smtp->setEncryption($settings['smtp_security']);
                }

                $this->mailer = new Mailer($smtp);
            }
        }
    }

    /**
     * Determine if an account is loaded
     *
     * @return boolean
     */
    public function isLoaded()
    {
        return ((null !== $this->imap) && (null !== $this->mailer));
    }

    /**
     * Determine if imap is loaded
     *
     * @return boolean
     */
    public function isImapLoaded()
    {
        return (null !== $this->imap);
    }

    /**
     * Determine if mailer is loaded
     *
     * @return boolean
     */
    public function isMailerLoaded()
    {
        return (null !== $this->mailer);
    }

    /**
     * Get imap resource
     *
     * @return Imap
     */
    public function imap()
    {
        return $this->imap;
    }

    /**
     * Get mailer object
     *
     * @return Mailer
     */
    public function mailer()
    {
        return $this->mailer;
    }

    /**
     * Set folder
     *
     * @param  string $folder
     * @return Mail
     */
    public function setFolder($folder)
    {
        $this->imap->setFolder($folder);
        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->imap->getFolder();
    }

    /**
     * Get folders
     *
     * @param string $pattern
     * @return array
     */
    public function getFolders($pattern = '*')
    {
        if (empty($this->mailboxes)) {
            $this->getMailboxes($pattern);
        }

        $folders = [];

        foreach ($this->mailboxes as $mailbox) {
            $this->parseFolders($mailbox, $folders);
        }

        return $folders;
    }

    /**
     * Get mailboxes
     *
     * @param string $pattern
     * @return array
     */
    public function getMailboxes($pattern = '*')
    {
        $this->mailboxes = $this->imap->listMailboxes($pattern);
        return $this->mailboxes;
    }

    /**
     * Parse folders
     *
     * @param  string $folder
     * @param  array  $result
     * @return void
     */
    protected function parseFolders($folder, &$result)
    {
        if (strpos($folder, '}') !== false) {
            $folder = substr($folder, (strpos($folder, '}') + 1));
        }
        if (strpos($folder, '/') !== false) {
            $f = substr($folder, 0, strpos($folder, '/'));
            $s = substr($folder, (strpos($folder, '/') + 1));
            if (!isset($result[$f])) {
                $result[$f] = [
                    'folder'     => $f,
                    'subfolders' => []
                ];
            }
            $this->parseFolders($s, $result[$f]['subfolders']);
        } else {
            $result[$folder] = [
                'folder'     => $folder,
                'subfolders' => []
            ];
        }
    }

}