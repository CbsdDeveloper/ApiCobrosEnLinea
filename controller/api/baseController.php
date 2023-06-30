<?php

class BaseController

{

    /**

     * __call magic method.

     */

    public function __call($name, $arguments)

    {

        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }

    /**

     * Get URI elements.

     *

     * @return array

     */

    protected function getUriSegments()

    {

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $uri = explode('/', $uri);

        return $uri;
    }

    /**

     * Get querystring params.

     *

     * @return array

     */

    protected function getQueryStringParams()

    {
        $result = array();
        parse_str($_SERVER['QUERY_STRING'], $result);
        return $result;
    }
    
    /**

     * Obtiene el body en peticion post

     *

     * @return array

     */

    protected function getPostBody()

    {

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        return $request;
    }

    /**

     * Send API output.

     *

     * @param mixed $data

     * @param string $httpHeader

     */

    protected function sendOutput($data, $httpHeaders = array())

    {

        header_remove('Set-Cookie');

        if (is_array($httpHeaders) && count($httpHeaders)) {

            foreach ($httpHeaders as $httpHeader) {

                header($httpHeader);
            }
        }

        echo $data;

        exit;
    }
}
