<?php

class OrdenesCobroController extends BaseController

{

    /**
     * "/ordenesCobro/list" Endpoint - Obtiene todas las ordendes de cobro
     */
    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        // validar login de coopmego
        $autorizado = false;
        $login = $_SERVER['HTTP_AUTHORIZATION'];
        $accesos = explode(":", base64_decode($login));
        if ($accesos[0] == 'admin' && $accesos[1] == '123456') {
            $autorizado = true;
        }

        if ($autorizado) {

            if (strtoupper($requestMethod) == 'GET') {
                try {
                    $ordenesCobroModel = new OrdenesCobroModel();
                    $intLimit = 10;
                    if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                        $intLimit = $arrQueryStringParams['limit'];
                    }
                    $arrOrdenes = $ordenesCobroModel->getAllOrdenes($intLimit);
                    $responseData = json_encode($arrOrdenes);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . ' Something went wrong! Please contact support.';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
            } else {
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            }
        } else {
            $strErrorDesc = 'Se requiere autenticación';
            $strErrorHeader = 'HTTP/1.1 401 Unauthorized';
        }



        // send output

        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)

            );
        }
    }

    /**
     * "/ordenesCobro/getOrdenesPorCliente" Endpoint - Obtiene todas las ordendes de cobro de un cliente por medio de la cédula o le código de trámite
     */
    public function getOrdenesPorClienteAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        $postdata = file_get_contents("php://input");  // obteniendo datos de POST
        $solicitud = json_decode($postdata); // Decodificando datos recibidos en un objeto

        // print_r( $solicitud );
        // return;

        // validar login de coopmego
        $autorizado = false;
        $login = $_SERVER['HTTP_AUTHORIZATION'];
        $accesos = explode(":", base64_decode($login));
        if ($accesos[0] == 'admin' && $accesos[1] == '123456') {
            $autorizado = true;
        }

        if ($autorizado) {
            if (strtoupper($requestMethod) == 'POST') {
                try {
                    $ordenesCobroModel = new OrdenesCobroModel();
                    $intLimit = 10;
                    if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                        $intLimit = $arrQueryStringParams['limit'];
                    }
                    $arrOrdenes = $ordenesCobroModel->getOrdenesPendientesCliente($solicitud->valor_busqueda);
                    $responseData = json_encode($arrOrdenes);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . ' Something went wrong! Please contact support.';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
            } else {
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            }
        } else {
            $strErrorDesc = 'Se requiere autenticación';
            $strErrorHeader = 'HTTP/1.1 401 Unauthorized';
        }

        // send output

        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)

            );
        }
    }

    /**
     * "/ordenesCobro/setPagoDeuda" Endpoint - Realiza el pago de la deuda en base a su código y retorna la confirmación
     */
    public function setPagoDeudaAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        $postdata = file_get_contents("php://input");  // obteniendo datos de POST
        $solicitudPago = json_decode($postdata); // Decodificando datos recibidos en un objeto

        // print_r($solicitudPago);
        // return;

        // validar login de coopmego
        $autorizado = false;
        $login = $_SERVER['HTTP_AUTHORIZATION'];
        $accesos = explode(":", base64_decode($login));
        if ($accesos[0] == 'admin' && $accesos[1] == '123456') {
            $autorizado = true;
        }

        if ($autorizado) {
            if (strtoupper($requestMethod) == 'POST') {
                try {
                    $ordenesCobroModel = new OrdenesCobroModel();
                    $resultado = $ordenesCobroModel->setPagoOrden($solicitudPago);
                    $responseData = json_encode($resultado);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . ' Something went wrong! Please contact support.';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
            } else {
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            }
        } else {
            $strErrorDesc = 'Se requiere autenticación';
            $strErrorHeader = 'HTTP/1.1 401 Unauthorized';
        }

        // send output

        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)

            );
        }
    }

    /**
     * "/ordenesCobro/setReversoPago" Endpoint - Realiza el reverso del pago de la deuda en base a su código, actualiza el estado para dejarla pendiente y retorna la confirmación
     */
    public function setReversoPagoAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        $postdata = file_get_contents("php://input");  // obteniendo datos de POST
        $solicitudPago = json_decode($postdata); // Decodificando datos recibidos en un objeto

        // print_r($solicitudPago);
        // return;

        // validar login de coopmego
        $autorizado = false;
        $login = $_SERVER['HTTP_AUTHORIZATION'];
        $accesos = explode(":", base64_decode($login));
        if ($accesos[0] == 'admin' && $accesos[1] == '123456') {
            $autorizado = true;
        }

        if ($autorizado) {
            if (strtoupper($requestMethod) == 'POST') {
                try {
                    $ordenesCobroModel = new OrdenesCobroModel();
                    $resultado = $ordenesCobroModel->setReversoPago($solicitudPago);
                    $responseData = json_encode($resultado);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage() . ' Something went wrong! Please contact support.';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
            } else {
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            }
        } else {
            $strErrorDesc = 'Se requiere autenticación';
            $strErrorHeader = 'HTTP/1.1 401 Unauthorized';
        }

        // send output

        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(
                json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)

            );
        }
    }
}
