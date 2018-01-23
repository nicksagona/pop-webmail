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
use Pop\Http\Request;
use Pop\Http\Response;
use Pop\Mail;
use Pop\View\View;

/**
 * Main exception class
 *
 * @category   PopWebmail
 * @package    PopWebmail
 * @link       https://github.com/nicksagona/pop-webmail
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2018 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @version    0.0.1-alpha
 */
class Module extends \Pop\Module\Module
{

    /**
     * Pop Webmail Version
     * @var string
     */
    const VERSION = '0.0.1-alpha';

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

        //$this->initMail();

        if (null !== $this->application->router()) {
            $this->application->router()->addControllerParams(
                '*', [
                    'application' => $this->application,
                    'request'     => new Request(),
                    'response'    => new Response()
                ]
            );

            //$this->application->on('app.dispatch.pre', 'PopWebmail\Event\Auth::authenticate');
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
     * Initialize mail services
     *
     * @return void
     */
    protected function initMail()
    {
        $this->application->services->set('mailer_transport', [
            'call'   => 'Pop\Mail\Transport\Smtp',
            'params' => [
                'host' => $this->application->config['mail']['smtp']['host'],
                'port' => $this->application->config['mail']['smtp']['port']
            ]
        ]);

        $this->application->services()->set('mailer', [
            'call' => function($application) {
                $transport = $application->services->get('mailer_transport');
                $transport->setUsername($application->config['mail']['smtp']['username'])
                    ->setPassword($application->config['mail']['smtp']['password']);
                $transport->setEncryption($application->config['mail']['smtp']['security']);

                return new Mail\Mailer($transport);
            },
            'params' => [$this->application]
        ]);

        $this->application->services()->set('imap', [
            'call' => function($application) {
                $imap = new Mail\Client\Imap(
                    $application->config['mail']['imap']['host'], $application->config['mail']['imap']['port']
                );

                $imap->setUsername($application->config['mail']['imap']['username'])
                    ->setPassword($application->config['mail']['imap']['password'])
                    ->setFolder($application->config['mail']['imap']['folder'])
                    ->open('/ssl');

                return $imap;
            },
            'params' => [$this->application]
        ]);
    }

}