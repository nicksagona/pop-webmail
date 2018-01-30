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
use Pop\Cookie\Cookie;
use Pop\Session\Session;
use PopWebmail\Table;

/**
 * User model class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.0.1-alpha
 */
class User extends AbstractModel
{

    /**
     * Authenticate user
     *
     * @param  string $username
     * @param  string $password
     * @return boolean
     */
    public function authenticate($username, $password)
    {
        $user = Table\Users::findOne(['username' => $username]);

        $this->data['id']       = $user->id;
        $this->data['username'] = $user->username;

        return (isset($user->id) && password_verify($password, $user->password));
    }

    /**
     * Log in user to web session
     *
     * @param  int     $userId
     * @param  Session $session
     * @param  Cookie  $cookie
     * @return void
     */
    public function login($userId, Session $session, Cookie $cookie)
    {
        $user     = Table\Users::findById($userId);
        $userData = [
            'id'       => $user->id,
            'username' => $user->username
        ];

        $session->user = $userData;
        $cookie->set('user', json_encode($userData));
    }

    /**
     * Log out user from web session
     *
     * @param  Session $session
     * @param  Cookie  $cookie
     * @return void
     */
    public function logout(Session $session, Cookie $cookie)
    {
        $cookie->delete('user');
        $session->kill();
    }

    /**
     * Get user by id
     *
     * @param  int $id
     * @return array
     */
    public function getById($id)
    {
        $userData = [];
        $user     = Table\Users::findById($id);

        if (isset($user->id)) {
            $userData = $user->toArray();
        }

        return $userData;
    }

    /**
     * Update user
     *
     * @param  array $data
     * @return array
     */
    public function update(array $data)
    {
        $user     = Table\Users::findById($data['id']);
        $userData = [];

        if (isset($user->id)) {
            $user->username   = (!empty($data['username'])) ? $data['username'] : $user->username;
            $user->password   = (!empty($data['password'])) ? password_hash($data['password'], PASSWORD_BCRYPT) : $user->password;
            $user->email      = (!empty($data['email'])) ? $data['email'] : $user->email;
            $user->save();

            $userData = $user->toArray();
            unset($userData['password']);
        }

        return $userData;
    }

}