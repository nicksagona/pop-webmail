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
namespace PopWebmail\Form\Mail;

/**
 * Compose form class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.9-beta
 */
class Compose extends \Pop\Form\Form
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
        $this->setAttribute('class', 'mail-compose-form');
        $this->setAttribute('id', 'mail-compose-form');
    }

}