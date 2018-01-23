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
     * Constructor
     *
     * Instantiate the mail model object
     *
     * @param  Imap   $imap
     * @param  Mailer $mailer
     * @param  array  $data
     */
    public function __construct(Imap $imap, Mailer $mailer, array $data = [])
    {
        $this->imap   = $imap;
        $this->mailer = $mailer;

        parent::__construct($data);
    }

}