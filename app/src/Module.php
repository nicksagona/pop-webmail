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
namespace PopWebmail;

use Pop\Application;
use Pop\Cache;
use Pop\Db;
use Pop\Http\Request;
use Pop\Http\Response;
use Pop\View\View;

/**
 * Main exception class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.9-beta
 */
class Module extends \Pop\Module\Module
{

    /**
     * Pop Webmail Version
     * @var string
     */
    const VERSION = '0.9-beta';

    /**
     * Module name
     * @var string
     */
    protected $name = 'pop-webmail';

    /**
     * Register module
     *
     * @param  Application $application
     * @return Module
     */
    public function register(Application $application)
    {
        parent::register($application);

        $this->initDb($this->application->config()['database']);

        if (null !== $this->application->router()) {
            $this->application->router()->addControllerParams(
                '*', [
                    'application' => $this->application,
                    'request'     => new Request(),
                    'response'    => new Response()
                ]
            );

            $this->application->on('app.dispatch.pre', 'PopWebmail\Event\Auth::authenticate');
        }

        return $this;
    }

    /**
     * Error handler method
     *
     * @param  \Exception $exception
     * @return void
     */
    public function error(\Exception $exception)
    {
        $response = new Response();
        $message  = $exception->getMessage();

        if (substr($message, 0, 7) != 'Error: ') {
            $message = 'Error: ' . $message;
        }

        $view = new View(__DIR__ . '/../view/exception.phtml', ['message' => $message]);
        $view->title = $message;

        $response->setHeader('Content-Type', 'text/html');
        $response->setBody($view->render());

        $response->send(500);
        exit();
    }

    /**
     * Initialize database service
     *
     * @param  array $database
     * @throws \Pop\Db\Adapter\Exception
     * @return void
     */
    protected function initDb($database)
    {
        if (!empty($database['adapter'])) {
            $adapter = $database['adapter'];
            $options = [
                'database' => $database['database'],
                'type'     => $database['type']
            ];
            $check = Db\Db::check($adapter, $options);
            if (null !== $check) {
                throw new \Pop\Db\Adapter\Exception('Error: ' . $check);
            }
            $this->application->services()->set('database', [
                'call'   => 'Pop\Db\Db::connect',
                'params' => [
                    'adapter' => $adapter,
                    'options' => $options
                ]
            ]);
            if ($this->application->services()->isAvailable('database')) {
                Db\Record::setDb($this->application->getService('database'));
            }
        }
    }

}