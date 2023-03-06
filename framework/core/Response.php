<?php

declare(strict_types = 1);

namespace Framework\Core;

use \Exception;
use Framework\Core\App;
use Framework\Infrastructure\{Session, ErrorLogger};
use Framework\Interfaces\IJson;
use Framework\Interfaces\ILogger;
use Framework\Interfaces\IResponse;
use Framework\Utils\Str;

/**
 * Encapsulates all the logic to get response data, view, etc from the app request.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class Response implements IResponse {

    private string $_controllerName = '';
    private string $_viewName = '';
    private ILogger $_logger;

    protected $head;
    protected $body;
    protected $section;
    protected $footer;
    protected $title = SITE_TITLE;
    protected $layout = LAYOUT_DEFAULT;
    protected $outputBuffer;

    /**
     * Indicates the html head section.
     */
    private const HEAD = 'head';

    /**
     * Indicates the html body section
     */
    private const BODY = 'body';

    /**
     * Indicates an extra section within the body tag
     */
    private const SECTION = 'section';

    /**
     * Indicates the html footer section, where scripts and perhaps css links can be included to the page, just before the body closing tag.
     */
    private const FOOTER = 'footer';

    public function __construct(string $controllerName, string $viewName, public IJson $json) {
        global $inflection;

        $this->_controllerName = $controllerName;
        $this->_viewName = $viewName;
        $this->title = ucwords($inflection->spacirize($viewName));

        $this->_logger = new ErrorLogger();
    }

    /**
     * Gets the page title.
     * 
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }

    /**
     * Sets the page title.
     * 
     * @param string $title Specifies the title of the page.
     * 
     * @return void
     */
    public function setTitle(string $title) : void {
        $this->title = $title;
    }

    /**
     * Sets a layout to be used for the current controller or page.
     * 
     * @param string $path Specifies the path (controller name and view name) of the layout to be included.
     * 
     * @return void
     */
    public function setLayout(string $path) : void {
        $this->layout = $path;
    }

    /**
     * Sets a section to be included in the layout file.
     * 
     * @param string $type Specifies the section of the layout to be included.
     * 
     * @return mixed
     */
    public function getContent(string $type) : mixed {
        if (trim($type) == self::HEAD)
            return $this->head;
        if (trim($type) == self::BODY)
            return $this->body;
        if (trim($type) == self::FOOTER)
            return $this->footer;
        if (trim($type) == self::SECTION)
            return $this->section;

        return null;
    }

    /**
     * Sets a section to be included in the layout file.
     * 
     * @param string $type Specifies the section of the layout to be included.
     * 
     * @return void
     */
    public function section(string $type) {
        $this->outputBuffer = $type;
        ob_start();
    }

    /**
     * Ends and cleans the section already set with the section method.
     */
    public function end() {
        if (in_array($this->outputBuffer, [self::HEAD, self::BODY, self::FOOTER, self::SECTION])) {

            if ($this->outputBuffer == self::HEAD) {
                $this->head = ob_get_clean();
            } elseif ($this->outputBuffer == self::BODY) {
                $this->body = ob_get_clean();
            } elseif ($this->outputBuffer == self::FOOTER) {
                $this->footer = ob_get_clean();            
            } elseif ($this->outputBuffer == self::SECTION) {
                $this->section = ob_get_clean();
            }

        } else {
            App::end('You must first call the start method.');
        }
    }

    /**
     * Displays a page to the user. If the view name was not found, \app\src\views\error\not_found.php file is displayed.
     * 
     * @param string $viewName [Optional] Specifies the view name to be displayed. Use the containing folder name followed by the actual view name to display.
     * 
     * @return void
     */
    public function view(string $viewName = '') {
        if ($viewName) {
            $viewNameArray  = explode((Str::contains($viewName, '.') ? '.' : '/'), $viewName);
            $viewPath = implode(DS, $viewNameArray);
        } else {
            $viewName = $this->_viewName;
            $viewPath = $this->_controllerName . DS . $this->_viewName;
        }
        $viewLocation = \mb_strtolower(PATH_APP_VIEWS . DS . $viewPath . '.php');
        // check if the view name exists
        if (file_exists($viewLocation)) {
            // render body
            include_once($viewLocation);
        } else {
            if (IS_DEVELOPMENT) {
                Session::set(APP_MESSAGE, 'There was no view found with the name "' . $viewName . '.php"');
                Session::set(APP_MESSAGE_TYPE, 'danger');
            } else {
                $this->_logger->log('Error rendering view at location: ' . $viewLocation);
            }
            include_once(PATH_APP_VIEWS . DS . 'error' . DS . 'not_found.php');
        }
            
        // render layout file
        include_once(PATH_APP_VIEWS_SHARED . DS . $this->layout . '.php');
    }

    /**
     * Displays json encoded result.
     * 
     * @param bool $hasError Sets the hasError to the value passed.
     * @param string $message Specifies the message sent to the user.
     * @param array $data Specifies the data to be sent along the json output.
     * 
     * @return void
     */
    public function json(bool $hasError, string $message, array $data = null) {
        try {
            header('Content-Type: application/json');
        } catch (Exception $e) {
            $this->_logger->log('Error setting Content-Type: application/json: ' . $e->getMessage());
        }
        echo json_encode(['hasError' => $hasError, 'message' => $message, 'data' => $data]);
    }

    public function jsonUnsupportedRequest(string $supportedRequestType) {
        http_response_code(405);
        $this->json(true, Str::toUpper($_SERVER['REQUEST_METHOD']) . " method not allowed. Only a {$supportedRequestType} is supported.");
    }

    /**
     * Removes all HTML tags and displays json encoded result.
     * 
     * @param bool $hasError Sets the hasError to the value passed.
     * @param string $message Specifies the message sent to the user.
     * @param array $data Specifies the data to be sent along the json output.
     * 
     * @return void
     */
    public function jsonRemoveHTML(bool $hasError, string $message, array $data = []) {
        $message = str_replace("<ul class=\"validation-errors\">\r\n", "", $message);
        $message = str_replace("</li>\r\n", "\r\n", $message);
        $message = str_replace("</li></ul>", "", $message);
        $message = str_replace("<li>", "", $message);

        $this->json($hasError, $message, $data);
    }

    /**
     * Redirects user to the specified location, within or outside the app. By default, users are generally redirected to links within the app.
     * 
     * @param string $location The desired location the user is navigating to.
     * @param bool $isExternal Indicates if the redirect should be within the app.
     * 
     * @return void
     */
    public static function redirect(string $location = '', bool $isExternal = false) {
        if (Str::contains($location, APP_BASE_URL)) $location = str_replace(APP_BASE_URL, '', $location);
        $url = ($isExternal ? '' : APP_BASE_URL) . $location;
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit();
        } else {
            $javaScriptRedirect = '
                <script>
                    window.location.href = "' . $url . '";                    
                </script>
                <noscript>
                    <meta http-equiv="refresh" content="0;url=' . $url . '" />
                </noscript>
            ';
            echo $javaScriptRedirect;
            exit;
        }
    }

}