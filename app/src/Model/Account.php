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
use PopWebmail\Table;

/**
 * Account model class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.9-beta
 */
class Account extends AbstractModel
{

    /**
     * Get all accounts
     *
     * @param  int    $page
     * @param  int    $limit
     * @param  string $sort
     * @return array
     */
    public function getAll($page = 1, $limit = 25, $sort = null)
    {
        if (((int)$limit > 0) && ((int)$page > 0)) {
            $page = ((int)$page > 1) ? ($page * $limit) - $limit : null;
        } else {
            $page  = null;
            $limit = null;
        }

        if (null !== $sort) {
            if (substr($sort, 0, 1) == '-') {
                $sort  = substr($sort, 1);
                $order = 'DESC';
            } else {
                $order = 'ASC';
            }
            $orderBy = $sort . ' ' . $order;
        } else {
            $orderBy = null;
        }

        return Table\Accounts::findAll([
            'offset' => $page,
            'limit'  => $limit,
            'order'  => $orderBy
        ])->toArray();
    }

    /**
     * Get user by id
     *
     * @param  int $id
     * @return array
     */
    public function getById($id)
    {
        $accountData = [];
        $account     = Table\Accounts::findById($id);

        if (isset($account->id)) {
            $accountData = $account->toArray();
            if (!empty($accountData['imap_password'])) {
                $accountData['imap_password'] = base64_decode($accountData['imap_password']);
            }
            if (!empty($accountData['smtp_password'])) {
                $accountData['smtp_password'] = base64_decode($accountData['smtp_password']);
            }
        }

        $this->data = array_merge($this->data, $accountData);

        return $accountData;
    }

    /**
     * Create new account
     *
     * @param  array $data
     * @return int
     */
    public function create(array $data)
    {
        if (!empty($data['default'])) {
            $accounts = Table\Accounts::findAll();
            foreach ($accounts as $acct) {
                $acct->default = 0;
                $acct->save();
            }
        }

        $account = new Table\Accounts([
            'name'             => html_entity_decode(strip_tags($data['name']), ENT_QUOTES, 'UTF-8'),
            'imap_host'        => (!empty($data['imap_host'])) ? $data['imap_host'] : null,
            'imap_port'        => (!empty($data['imap_port'])) ? $data['imap_port'] : null,
            'imap_username'    => (!empty($data['imap_username'])) ? $data['imap_username'] : null,
            'imap_password'    => (!empty($data['imap_password'])) ? base64_encode($data['imap_password']) : null,
            'imap_flags'       => (!empty($data['imap_flags'])) ? $data['imap_flags'] : null,
            'smtp_host'        => (!empty($data['smtp_host'])) ? $data['smtp_host'] : null,
            'smtp_port'        => (!empty($data['smtp_port'])) ? $data['smtp_port'] : null,
            'smtp_username'    => (!empty($data['smtp_username'])) ? $data['smtp_username'] : null,
            'smtp_password'    => (!empty($data['smtp_password'])) ? base64_encode($data['smtp_password']) : null,
            'smtp_security'    => (!empty($data['smtp_security'])) ? $data['smtp_security'] : null,
            'html_signature'   => (!empty($data['html_signature'])) ? $data['html_signature'] : null,
            'text_signature'   => (!empty($data['text_signature'])) ? $data['text_signature'] : null,
            'signature_on_all' => (!empty($data['signature_on_all'])) ? 1 : 0,
            'default'          => (!empty($data['default'])) ? 1 : 0
        ]);
        $account->save();

        return $account->id;
    }

    /**
     * Update account
     *
     * @param  array $data
     * @return array
     */
    public function update(array $data)
    {
        $account     = Table\Accounts::findById($data['id']);
        $accountData = [];

        if (isset($account->id)) {
            if (!empty($data['default'])) {
                $accounts = Table\Accounts::findBy(['id!=' => $account->id]);
                foreach ($accounts as $acct) {
                    $acct->default = 0;
                    $acct->save();
                }
            }

            $account->name             = (!empty($data['name'])) ? html_entity_decode(strip_tags($data['name']), ENT_QUOTES, 'UTF-8') : $account->name;
            $account->imap_host        = (!empty($data['imap_host'])) ? $data['imap_host'] : null;
            $account->imap_port        = (!empty($data['imap_port'])) ? $data['imap_port'] : null;
            $account->imap_username    = (!empty($data['imap_username'])) ? $data['imap_username'] : null;
            $account->imap_password    = (!empty($data['imap_password'])) ? base64_encode($data['imap_password']) : null;
            $account->imap_flags       = (!empty($data['imap_flags'])) ? $data['imap_flags'] : null;
            $account->smtp_host        = (!empty($data['smtp_host'])) ? $data['smtp_host'] : null;
            $account->smtp_port        = (!empty($data['smtp_port'])) ? $data['smtp_port'] : null;
            $account->smtp_username    = (!empty($data['smtp_username'])) ? $data['smtp_username'] : null;
            $account->smtp_password    = (!empty($data['smtp_password'])) ? base64_encode($data['smtp_password']) : null;
            $account->smtp_security    = (!empty($data['smtp_security'])) ? $data['smtp_security'] : null;
            $account->html_signature   = (!empty($data['html_signature'])) ? $data['html_signature'] : null;
            $account->text_signature   = (!empty($data['text_signature'])) ? $data['text_signature'] : null;
            $account->signature_on_all = (!empty($data['signature_on_all'])) ? 1 : $account->signature_on_all;
            $account->default          = (!empty($data['imap_host'])) ? 1 : $account->default;
            $account->save();

            $accountData = $account->toArray();
            unset($accountData['password']);
        }

        return $accountData;
    }

    /**
     * Delete account
     *
     * @param  int|array $data
     * @return void
     */
    public function delete($data)
    {
        if (is_array($data)) {
            foreach ($data as $id) {
                $account = Table\Accounts::findById($id);
                if (isset($account->id)) {
                    $account->delete();
                }
            }
        } else if (is_numeric($data)) {
            $account = Table\Accounts::findById($data);
            if (isset($account->id)) {
                $account->delete();
            }
        }
    }

    /**
     * Determine if accounts have pages
     *
     * @param  int $limit
     * @return boolean
     */
    public function hasPages($limit)
    {
        return (Table\Accounts::getTotal() > $limit);
    }

    /**
     * Get count of accounts
     *
     * @return int
     */
    public function getCount()
    {
        return Table\Accounts::getTotal();
    }

}