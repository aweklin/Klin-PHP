<?php

namespace Framework\Interfaces;

/**
 * Wraps methods to handle response.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
interface IResponse {

    /**
     * Gets the page title.
     * 
     * @return string
     */
    function getTitle() : string;

    /**
     * Sets the page title.
     * 
     * @param string $title Specifies the title of the page.
     * 
     * @return void
     */
    function setTitle(string $title) : void;

    /**
     * Sets a layout to be used for the current controller or page.
     * 
     * @param string $path Specifies the path (controller name and view name) of the layout to be included.
     * 
     * @return void
     */
    function setLayout(string $path) : void;

    /**
     * Sets a section to be included in the layout file.
     * 
     * @param string $type Specifies the section of the layout to be included.
     * 
     * @return mixed
     */
    function getContent(string $type) : mixed;

    /**
     * Sets a section to be included in the layout file.
     * 
     * @param string $type Specifies the section of the layout to be included.
     * 
     * @return void
     */
    function section(string $type);

    /**
     * Ends and cleans the section already set with the section method.
     */
    function end();

    /**
     * Displays a page to the user. If the view name was not found, \app\src\views\error\not_found.php file is displayed.
     * 
     * @param string $viewName [Optional] Specifies the view name to be displayed. Use the containing folder name followed by the actual view name to display.
     * 
     * @return void
     */
    function view(string $viewName = '');

    /**
     * Displays json encoded result.
     * 
     * @param bool $hasError Sets the hasError to the value passed.
     * @param string $message Specifies the message sent to the user.
     * @param array $data Specifies the data to be sent along the json output.
     * 
     * @return void
     */
    function json(bool $hasError, string $message, array $data = null);
    function jsonUnsupportedRequest(string $supportedRequestType);

    /**
     * Removes all HTML tags and displays json encoded result.
     * 
     * @param bool $hasError Sets the hasError to the value passed.
     * @param string $message Specifies the message sent to the user.
     * @param array $data Specifies the data to be sent along the json output.
     * 
     * @return void
     */
    function jsonRemoveHTML(bool $hasError, string $message, array $data = []);

    /**
     * Redirects user to the specified location, within or outside the app. By default, users are generally redirected to links within the app.
     * 
     * @param string $location The desired location the user is navigating to.
     * @param bool $isExternal Indicates if the redirect should be within the app.
     * 
     * @return void
     */
    static function redirect(string $location = '', bool $isExternal = false);
}