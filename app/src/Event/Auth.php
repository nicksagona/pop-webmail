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
namespace PopWebmail\Event;

use Pop\Application;
use Pop\Http\Response;
use Pop\Session\Session;

/**
 * Auth event class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.9-beta
 */
class Auth
{

    /**
     * Public actions
     */
    protected static $publicActions = [
        'PopWebmail\Controller\IndexController' => [
            'login'
        ]
    ];

    /**
     * Authenticate the user session
     *
     * @param  Application $application
     * @return void
     */
    public static function authenticate(Application $application)
    {
        if (!empty($application->router()->getController())) {
            $sess   = $application->services['session'];
            $ctrl   = $application->router()->getControllerClass();
            $action = $application->router()->getRouteMatch()->getAction();
            if (self::isPublicAction($ctrl, $action) && isset($sess->user)) {
                Response::redirect('/');
                exit();
            } else if (!self::isPublicAction($ctrl, $action) && !isset($sess->user)) {
                Response::redirect('/login');
                exit();
            }
        }
    }

    /**
     * Check if public action
     *
     * @param  string $controller
     * @param  string $action
     * @return boolean
     */
    public static function isPublicAction($controller, $action)
    {
        return (isset(self::$publicActions[$controller]) && in_array($action, self::$publicActions[$controller]));
    }

}