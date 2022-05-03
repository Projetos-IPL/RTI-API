<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/Auth/AuthUtils.php';

    abstract class ControllerUtils {

        /**
         * Função para validar o corpo de um pedido
         * @param array $reqBody Corpo do pedido
         * @param array | string $reqBodySpec Especificação do corpo do pedido
         * @return bool Verdadeiro se o corpo do pedido respeitar a especificação, falso se não
         */
        public static function validateRequestBody(array $reqBody, array | string $reqBodySpec) : bool
        {
            if (gettype($reqBodySpec) == 'string') {
                $reqBodySpec = array($reqBodySpec);
            }

            $valid = true;
            foreach ($reqBodySpec as $prop) {
                if (!isset($reqBody[$prop])) {
                    $valid = false;
                    break;
                }
            }
            if (count($reqBody) != count($reqBodySpec)) {
                $valid = false;
            }
            return $valid;
        }

        /**
         * Função para validar os headers de um pedido, os headers existentes na especificação devem existir no pedido.
         * @param array $reqHeaders
         * @param array | string $reqHeadersSpec
         * @return bool Verdadeiro se o pedido contém os cabeçalhos existentes na especificação, falsos se não.
         */
        public static function validateRequestHeaders(array $reqHeaders, array | string  $reqHeadersSpec) : bool
        {
            if (gettype($reqHeadersSpec) == 'string') {
                $reqHeadersSpec = array($reqHeadersSpec);
            }

            $valid = true;
            foreach ($reqHeadersSpec as $header) {
                if (!isset($reqHeaders[$header])) {
                    $valid = false;
                    break;
                }
            }
            return $valid;
        }

        /** Função para autorizar um pedido, faz validação de token caso endpoint esteja configurado para privado.
         * @param array $AUTH_MAP mapa de autorizações do endpoint
         * @param array $REQ_HEADERS cabeçalhos do pedido
         * @throws InvalidTokenException
         * @throws MissingTokenException
         */
        public static function authorize(array $AUTH_MAP, array $REQ_HEADERS)
        {
            // Métodos não especificados no AUTHORIZATION_MAP são considerados privados, logo são verificados
            if (!isset($AUTH_MAP[$_SERVER['REQUEST_METHOD']]) || !$AUTH_MAP[$_SERVER['REQUEST_METHOD']]) {
                if (!isset($REQ_HEADERS[X_AUTH_TOKEN])) {
                    throw new MissingTokenException();
                }
                if (!AuthUtils::verifyJWT($REQ_HEADERS[X_AUTH_TOKEN])) {
                    throw new InvalidTokenException();
                }
            }
        }

        /** Função para obter os cabeçalhos do pedido http
         * @return array Array com os pedidos http
         */
        public static function get_HTTP_request_headers() : array
        {
            $HTTP_headers = array();
            foreach($_SERVER as $key => $value) {
                if (!str_starts_with($key, 'HTTP_')) {
                    continue;
                }
                $single_header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $HTTP_headers[$single_header] = $value;
            }
            return $HTTP_headers;
        }



    }