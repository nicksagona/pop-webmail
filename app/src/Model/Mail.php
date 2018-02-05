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
     * Mailboxes
     *
     * @var array
     */
    protected $mailboxes = null;

    /**
     * Current mailbox total
     *
     * @var int
     */
    protected $mailboxTotal = null;

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
     * Open IMAP connection
     *
     * @param string $flags
     * @param int    $options
     * @param int    $retries
     * @param array  $params
     * @return Mail
     */
    public function open($flags = null, $options = null, $retries = null, array $params = null)
    {
        $this->imap->open($flags, $options, $retries, $params);
        return $this;
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
     * Fetch all mail
     *
     * @param  int     $page
     * @param  int     $limit
     * @param  int     $sort
     * @param  boolean $reverse
     * @param  array   $search
     * @return array
     */
    public function fetchAll($page = null, $limit = null, $sort = SORTDATE, $reverse = true, array $search = null)
    {
        $searchString = '';
        if (!empty($search)) {
            foreach ($search as $key => $value) {
                switch ($key) {
                    case 'STATUS':
                        $searchString .= $value . ' ';
                        break;
                    default:
                        $searchString .= $key . ' "' . $value . '" ';
                }
            }
        } else {
            $searchString .= 'ALL';
        }

        $ids                = $this->imap->getMessageIdsBy($sort, $reverse, SE_UID, $searchString);
        $this->mailboxTotal = count($ids);
        $messages           = $this->getMessageOverview($ids, $page, $limit);
        return $messages;
    }

    /**
     * Fetch mail by subject
     *
     * @param  string  $subject
     * @param  int     $page
     * @param  int     $limit
     * @param  int     $sort
     * @param  boolean $reverse
     * @return array
     */
    public function fetchBySubject($subject, $page = null, $limit = null, $sort = SORTDATE, $reverse = true)
    {
        $ids      = $this->imap->getMessageIdsBy($sort, $reverse, SE_UID, 'SUBJECT "' . $subject . '"');
        $messages = $this->getMessageOverview($ids, $page, $limit);
        return $messages;
    }

    /**
     * Fetch mail by from address
     *
     * @param  string  $from
     * @param  int     $page
     * @param  int     $limit
     * @param  int     $sort
     * @param  boolean $reverse
     * @return array
     */
    public function fetchFrom($from, $page = null, $limit = null, $sort = SORTDATE, $reverse = true)
    {
        $ids      = $this->imap->getMessageIdsBy($sort, $reverse, SE_UID, 'FROM "' . $from . '"');
        $messages = $this->getMessageOverview($ids, $page, $limit);
        return $messages;
    }

    /**
     * Fetch mail by id
     *
     * @param  int $id
     * @return \stdClass
     */
    public function fetchById($id)
    {
        $this->imap->markAsRead($id);

        $headers = $this->imap->getMessageHeadersById($id);
        $parts   = $this->imap->getMessageParts($id);

        if (!isset($headers->subject) && isset($headers->Subject)) {
            $headers->subject = $headers->Subject;
        }
        if (!isset($headers->Subject) && isset($headers->subject)) {
            $headers->Subject = $headers->subject;
        }

        $message = new \stdClass();
        $message->headers        = $headers;
        $message->parts          = $parts;
        $message->hasAttachments = false;

        foreach ($parts as $i => $part) {
            if ($part->attachment) {
                $message->hasAttachments = true;
                break;
            }
        }

        return $message;
    }

    /**
     * Fetch message attachment
     *
     * @param  int $id
     * @param  int $i
     * @return \stdClass
     */
    public function fetchAttachment($id, $i)
    {
        $file        = $this->imap->getMessageParts($id)[$i];
        $disposition = null;

        if ((strtolower(substr($file->basename, -4)) == '.pdf') || (stripos($file->content, '%PDF-') !== false)) {
            $type = 'application/pdf';
        } else if ((strtolower(substr($file->basename, -4)) == '.jpg') || (strtolower(substr($file->basename, -5)) == '.jpeg')) {
            $type = 'image/jpeg';
        } else if (strtolower(substr($file->basename, -4)) == '.gif') {
            $type = 'image/gif';
        } else if (strtolower(substr($file->basename, -4)) == '.png') {
            $type = 'image/png';
        } else if ((strtolower(substr($file->basename, -4)) == '.doc') || (strtolower(substr($file->basename, -5)) == '.docx')) {
            $type = 'application/msword';
        } else if ((strtolower(substr($file->basename, -4)) == '.xls') || (strtolower(substr($file->basename, -5)) == '.xlsx')) {
            $type = 'application/vnd.ms-excel';
        } else {
            $type   = $file->type;
            $disposition = 'attachment; ';
        }

        if (empty($file->basename)) {
            $filename = 'attachment';
            if (stripos($file->content, '%PDF-') !== false) {
                $filename .= '.pdf';
            }
        } else {
            $filename = str_replace(['"', ';'], ['', ''], $file->basename);
        }

        $attachment              = new \stdClass();
        $attachment->type        = $type;
        $attachment->disposition = $disposition;
        $attachment->filename    = $filename;
        $attachment->content     = $file->content;

        return $attachment;
    }

    /**
     * Get message overview
     *
     * @param  array $ids
     * @param  int   $page
     * @param  int   $limit
     * @return array
     */
    public function getMessageOverview(array $ids, $page = null, $limit = null)
    {
        $messages = [];
        $start    = ((null !== $page) && ((int)$page > 1)) ? ($page * $limit) - $limit : 0;
        $end      = $start + ((null !== $limit) ? $limit : count($ids));

        for ($i = $start; $i < $end; $i++) {
            if (isset($ids[$i])) {
                $messages[$ids[$i]] = $this->imap->getOverview($ids[$i]);
                $structure          = $this->imap->getMessageStructure($ids[$i]);
                $hasAttachment      = false;

                if (isset($messages[$ids[$i]][0]) && isset($messages[$ids[$i]][0]->subject)) {
                    $messages[$ids[$i]][0]->subject = $this->decodeText($messages[$ids[$i]][0]->subject);
                }

                if (isset($structure->parts)) {
                    foreach ($structure->parts as $part) {
                        if (isset($part->disposition) && (strtolower($part->disposition) == 'attachment')) {
                            $hasAttachment = true;
                            break;
                        }
                    }
                }

                $messages[$ids[$i]][0]->structure     = $structure;
                $messages[$ids[$i]][0]->hasAttachment = $hasAttachment;
            }
        }

        return $messages;
    }

    /**
     * Get current mailbox total
     *
     * @return int
     */
    public function getMailboxTotal()
    {
        return $this->mailboxTotal;
    }

    /**
     * Get number of unread messages
     *
     * @return int
     */
    public function getNumberOfUnread()
    {
        return $this->imap->getNumberOfUnreadMessages();
    }

    /**
     * Decode text
     *
     * @param  string $text
     * @return string
     */
    public function decodeText($text)
    {
        $decodedValues = imap_mime_header_decode($text);
        $decoded       = '';

        foreach ($decodedValues as $string) {
            $decoded .= $string->text;
        }

        return $decoded;
    }

    /**
     * Convert links in text
     *
     * @param  string  $string
     * @param  boolean $target
     * @return string
     */
    public function convertLinks($string, $target = null) {
        $target = (null !== $target) ? 'target="' . $target . '" ' : '';
        $string = preg_replace('/[ftp|http|https]+:\/\/[^\s]*/', '<a href="$0">$0</a>', $string);
        $string = preg_replace('/\s[\w]+[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,4})/', ' <a href="http://$0">$0</a>', $string);
        $string = preg_replace('/[a-zA-Z0-9\.\-\_+%]+@[a-zA-Z0-9\-\_\.]+\.[a-zA-Z]{2,4}/', '<a href="mailto:$0">$0</a>', $string);
        $string = str_replace(
            [
                'href="http:// ',
                'href="https:// ',
                '"> ',
                '<a '
            ],
            [
                'href="http://',
                'href="https://',
                '">',
                '<a ' . $target
            ],
            $string
        );
        return $string;
    }

    /**
     * Get message body content
     *
     * @param  array $parts
     * @return string
     */
    public function getContent($parts)
    {
        $text         = null;
        $html         = null;
        $fallBack     = null;
        $foundContent = null;

        foreach ($parts as $i => $part) {
            if (!$part->attachment) {
                $content = (base64_decode($part->content, true) !== false) ? base64_decode($part->content, true) : $part->content;
                if ($content == strip_tags($content)) {
                    $content = nl2br($this->convertLinks($content, true));
                }
                if (stripos($content, '<body')) {
                    $content = substr($content, stripos($content, '<body'));
                    $content = substr($content, (stripos($content, '>') + 1));
                    $content = trim(substr($content, 0, stripos($content, '</body>')));
                }
                if ($part->type == 'text/html') {
                    $html = $content;
                } else if ($part->type == 'text/plain') {
                    $text = $content;
                } else if ($part->type == 'multipart/alternative') {
                    if (isset($part->headers['Content-Type']) && (strpos($part->headers['Content-Type'], 'boundary=') !== false)) {
                        $boundary = str_replace('"', '', substr($part->headers['Content-Type'], (strpos($part->headers['Content-Type'], 'boundary=') + 9)));
                        $contentAry = explode('--' . $boundary, $content);
                        foreach ($contentAry as $c) {
                            if (strpos($c, 'text/html')) {
                                $html = $c;
                                if (strpos($html, "\r\n\r\n") !== false) {
                                    $html = substr($html, (strpos($html, "\r\n\r\n") + 4));
                                }
                            } else if (strpos($c, 'text/plain')) {
                                $text = $c;
                                if (strpos($text, "\r\n\r\n") !== false) {
                                    $text = substr($text, (strpos($text, "\r\n\r\n") + 4));
                                }
                            }
                        }
                    } else {
                        $text = $content;
                    }
                } else {
                    $fallBack = $content;
                }
            }
        }

        if (null !== $html) {
            $foundContent = $html;
        } else if (null !== $text) {
            $foundContent = $text;
        } else if (null !== $fallBack) {
            $foundContent = $fallBack;
        }

        return $foundContent;
    }

    /**
     * Get message attachments links
     *
     * @param  array $parts
     * @param  int   $id
     * @return array
     */
    public function getAttachmentLinks($parts, $id) {
        $attachments = [];

        foreach ($parts as $i => $part) {
            if ($part->attachment) {
                $attachments[] = '<a href="/mail/attachments/' . $id . '/' . ($i + 1) . '">' .
                    (!empty($part->basename) ? $part->basename : 'file_' . ($i + 1)) . '</a>';
            }
        }

        return $attachments;
    }

    /**
     * Process mailbox
     *
     * @param array $data
     * @param array $folders
     * @return void
     */
    public function process(array $data, $folders)
    {
        if (isset($data['process_mail']) && isset($data['mail_process_action'])) {
            switch ($data['mail_process_action']) {
                // Copy to folder
                case 3:
                    $this->imap->copyMessage($data['process_mail'], $data['move_folder_select']);
                    break;
                // Move to folder
                case 2:
                    $this->imap->moveMessage($data['process_mail'], $data['move_folder_select']);
                    break;
                // Mark as read
                case 1:
                    foreach ($data['process_mail'] as $id) {
                        $this->imap->markAsRead($id);
                    }
                    break;
                // Mark as unread
                case 0:
                    foreach ($data['process_mail'] as $id) {
                        $this->imap->markAsUnread($id);
                    }
                    break;
                // Move to trash
                case -1:
                    $trashFolder = null;

                    foreach ($folders as $folder) {
                        if (stripos($folder, 'trash') !== false) {
                            $trashFolder = substr($folder, (strpos($folder, '}') + 1));
                            break;
                        }
                    }

                    if (null === $trashFolder) {
                        foreach ($folders as $folder) {
                            if (stripos($folder, 'deleted') !== false) {
                                $trashFolder = substr($folder, (strpos($folder, '}') + 1));
                                break;
                            }
                        }
                    }
                    if (null !== $trashFolder) {
                        $this->imap->moveMessage($data['process_mail'], $trashFolder);
                    }
                    break;
                // Delete permanently
                case -2:
                    foreach ($data['process_mail'] as $id) {
                        $this->imap->deleteMessage($id);
                    }
                    break;
            }
        }
    }

    /**
     * Add folder
     *
     * @param array $data
     * @return void
     */
    public function addFolder(array $data)
    {
        if (isset($data['new_folder_name'])) {
            $newFolder = $data['new_folder_name'];
            if (isset($data['add_folder_to']) && ($data['add_folder_to'] != '----')) {
                $newFolder = $data['add_folder_to'] . '/' . $newFolder;
            }
            $this->imap->createMailbox($newFolder);
        }
    }

    /**
     * Rename folder
     *
     * @param array $data
     * @return void
     */
    public function renameFolder(array $data)
    {
        if (isset($data['folder_to_rename']) && ($data['folder_to_rename'] != '----') && !empty($data['rename_folder_to'])) {
            $oldFolder = $data['folder_to_rename'];
            $newFolder = $data['rename_folder_to'];
            $this->imap->renameMailbox($newFolder, $oldFolder);
        }
    }

    /**
     * Remove folder
     *
     * @param array $data
     * @return void
     */
    public function removeFolder(array $data)
    {
        if (isset($data['folder_to_remove']) && ($data['folder_to_remove'] != '----')) {
            $this->imap->deleteMailbox($data['folder_to_remove']);
        }
    }

    /**
     * Determine if the current mailbox has pages
     *
     * @param  int $limit
     * @return boolean
     */
    public function hasPages($limit)
    {
        return ((int)$this->mailboxTotal > $limit);
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