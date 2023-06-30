<?php

// Include database class
include_once PROJECT_ROOT_PATH . '/config/dbclass.php';

class OrdenesCobroModel
{

    /**
     * getAllOrdenes - Obtiene todas las ordendes de cobro
     */
    public function getAllOrdenes($limit)
    {

        $database = new Database();
        $database->query("SELECT * FROM recaudacion.tb_ordenescobro");
        $rows = $database->resultset();
        $database->closeConnection();
        return $rows;
    }

    /**
     * getAllOrdenes - Obtiene todas las ordendes de cobro pendienes de cobro (no cuenta las despachadas)
     */
    public function getAllOrdenesPendientes($limit)
    {

        $database = new Database();
        $database->query("SELECT * FROM recaudacion.tb_ordenescobro WHERE orden_estado != 'DESPACHADA'");
        $rows = $database->resultset();
        $database->closeConnection();
        return $rows;
    }

    /**
     * getOrdenPorCodigo - Obtiene una orden de cobro en base al código dado
     */
    public function getOrdenPorCodigo($codigo)
    {

        $database = new Database();
        $database->query("SELECT * FROM recaudacion.tb_ordenescobro WHERE orden_estado != 'DESPACHADA' AND orden_codigo=:orden_codigo");
        $database->bind('orden_codigo', $codigo);
        $row = $database->single();
        $database->closeConnection();
        return $row;
    }

    /**
     * getOrdenesPendientesCliente - Obtiene todas las ordendes de cobro pendienes de un cliente con su cédula o ruc (no cuenta las despachadas)
     */
    public function getOrdenesPendientesCliente($valor_busqueda)
    {

        $database = new Database();
        // $database->query("SELECT * FROM recaudacion.tb_ordenescobro WHERE orden_estado != 'DESPACHADA' AND (orden_doc_identidad = :orden_doc_identidad or orden_codigo=:orden_codigo)");
        $database->query("SELECT * FROM recaudacion.tb_ordenescobro WHERE (orden_estado != 'DESPACHADA' AND orden_estado != 'PAGADO') AND orden_doc_identidad = :orden_doc_identidad");
        $database->bind('orden_doc_identidad', $valor_busqueda);
        // $database->bind('orden_codigo', $valor_busqueda);
        $cliente = $database->single();
        $database->closeConnection();

        $ejemplo = array(
            "codigo_respuesta" => "C001",
            "descripcion_respuesta" => "Valores pendientes de pago",
            "cliente" => array(
                "identificacion" => "",
                "nombres" => "",
                "apellido_paterno" => "",
                "apellido_materno" => "",
            ),
            "deudas" => array(
                array(
                    "id_deuda" => "",
                    "secuencial_oriden" => "",
                    "secuencial_pago" => "",
                    "nombre_deuda" => "",
                    "valor_deuda" => "",
                    "fecha_emision" => "",
                    "fecha_vencimiento" => "",
                    "detalle_deuda" => array(
                        array(
                            "id_rubro" => "130102",
                            "nombre_rubro" => "",
                            "valor_rubro" => ""
                        )
                    )
                )
            )
        );

        if ($cliente) { // validando que existan registros

            $nombres = $cliente["orden_cliente_nombre"];
            $apellido_paterno = "";
            $apellido_materno = "";

            $result = array(
                "codigo_respuesta" => "C001",
                "descripcion_respuesta" => "Valores pendientes de pago",
                "cliente" => array(
                    "identificacion" => $cliente["orden_doc_identidad"],
                    "nombres" => $nombres,
                    "apellido_paterno" => $apellido_paterno,
                    "apellido_materno" => $apellido_materno,
                ),
                "deudas" => array()
            );

            //Obteniendo todas las deudas actuales
            $database = new Database();
            $database->query("SELECT * FROM recaudacion.tb_ordenescobro WHERE (orden_estado != 'DESPACHADA' AND orden_estado != 'PAGADO') AND orden_doc_identidad = :orden_doc_identidad");
            $database->bind('orden_doc_identidad', $valor_busqueda);
            $deudas = $database->resultset();
            $database->closeConnection();

            for ($i = 0; $i < count($deudas); $i++) {
                $nueva_deuda = array(
                    "id_deuda" => $deudas[$i]["orden_codigo"],
                    "secuencial_oriden" => $i + 1,
                    "secuencial_pago" => 0,
                    "nombre_deuda" => $deudas[$i]["orden_concepto"],
                    "valor_deuda" => (float)$deudas[$i]["orden_total"],
                    "fecha_emision" => date('Ymd', strtotime($deudas[$i]["fecha_generado"])),
                    "fecha_vencimiento" => date('Ymd', strtotime($deudas[$i]["fecha_generado"])),
                    "detalle_deuda" => array(
                        array(
                            "id_rubro" => "130102",
                            "nombre_rubro" => "Permisos, Licencias y Patentes",
                            "valor_rubro" => (float)$deudas[$i]["orden_total"]
                        )
                    )
                );
                array_push($result["deudas"], $nueva_deuda);
            }
        } else {
            $result = array(
                "codigo_respuesta" => "C001",
                "descripcion_respuesta" => "Cliente no encontrado",
                "cliente" => array(
                    "identificacion" => "",
                    "nombres" => "",
                    "apellido_paterno" => "",
                    "apellido_materno" => "",
                ),
                "deudas" => array(
                    array(
                        "id_deuda" => "",
                        "secuencial_oriden" => "",
                        "secuencial_pago" => "",
                        "nombre_deuda" => "",
                        "valor_deuda" => "",
                        "fecha_emision" => "",
                        "fecha_vencimiento" => "",
                        "detalle_deuda" => array(
                            array(
                                "id_rubro" => "130102",
                                "nombre_rubro" => "",
                                "valor_rubro" => ""
                            )
                        )
                    )
                )
            );
        }

        // Procesando información para acoplarala a la ficha técnica de coopmego
        // if (count($rows) == 0) {
        //     return $result;
        // }

        return $result;
    }

    /**
     * setPagoOrden - Realiza el pago de la orden y cambia el estado en el sistema
     */
    public function setPagoOrden($pago)
    {

        $database = new Database();
        // $database->query("SELECT * FROM recaudacion.tb_ordenescobro WHERE (orden_estado != 'DESPACHADA' AND orden_estado != 'PAGADO') AND orden_codigo=:orden_codigo");
        $database->query("SELECT * FROM recaudacion.tb_ordenescobro WHERE orden_codigo=:orden_codigo");
        $database->bind('orden_codigo', $pago->id_deuda);
        $orden = $database->single();
        $database->closeConnection();

        if ($orden) { // validando que existan registros

            if ((float)$orden["orden_total"] != (float)$pago->valor_deuda) { // Validando que el valor a pagar sea el mismo del sistema
                $result = array(
                    "codigo_respuesta" => "E002",
                    "descripcion_respuesta" => "Montos no coinciden, enviando $". $pago->valor_deuda ." es diferente a $" . $orden["orden_total"]
                );
                return $result;
            }

            // TODO: crear tabla para registro de pagos en linea de coopmego y guardar los campos

            // Actualización de valores en la tabla de ordenes de cobro
            $database = new Database();
            $database->query("UPDATE recaudacion.tb_ordenescobro SET orden_estado = 'DESPACHADA' WHERE orden_codigo=:orden_codigo");
            $database->bind('orden_codigo', $pago->id_deuda);
            $orden = $database->execute();
            $database->closeConnection();

            $result = array(
                "codigo_respuesta" => "P001",
                "descripcion_respuesta" => "PAGO EXITOSO"
            );
        } else {
            $result = array(
                "codigo_respuesta" => "E001",
                "descripcion_respuesta" => "Orden no encontrada"
            );
        }

        return $result;
    }
}
