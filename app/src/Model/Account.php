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
 * @version    0.0.1-alpha
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
        }

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
        $account = new Table\Accounts([
            'name' => html_entity_decode(strip_tags($data['name']), ENT_QUOTES, 'UTF-8')
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
            $account->name  = (!empty($data['name'])) ? html_entity_decode(strip_tags($data['name']), ENT_QUOTES, 'UTF-8') : $account->name;
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

}