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
namespace PopWebmail\Form;

use PopWebmail\Table\Users;
use Pop\Validator;

/**
 * Profile form class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.0.1-alpha
 */
class Profile extends \Pop\Form\Form
{

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  array  $fields
     * @param  string $action
     * @param  string $method
     */
    public function __construct(array $fields = null, $action = null, $method = 'post')
    {
        parent::__construct($fields, $action, $method);
        $this->setAttribute('class', 'profile-form');
        $this->setAttribute('id', 'profile-form');
    }

    /**
     * Add validators
     *
     * @return Profile
     */
    public function addValidators()
    {
        // Check for dupe username
        $user = null;
        if (null !== $this->username) {
            $user = Users::findOne(['username' => $this->username]);
            if (isset($user->id) && ($this->id != $user->id)) {
                $this->getField('username')
                    ->addValidator(new Validator\NotEqual($this->username, 'That username is not allowed.'));
            }
        }

        // Check password matches
        if (!empty($this->password)) {
            $this->getField('password')
                ->addValidator(new Validator\LengthGte(6));

            $this->getField('password2')
                ->setRequired(true)
                ->addValidator(new Validator\Equal($this->password, 'The passwords do not match.'));
        }

        return $this;
    }

}