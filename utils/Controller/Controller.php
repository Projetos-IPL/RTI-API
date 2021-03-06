<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller/ControllerUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller/exceptions/MissingRequiredHeadersException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller/exceptions/InvalidRequestBodyException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller/exceptions/HttpRequestMethodNotAllowedException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/Controller/exceptions/InvalidURLParamsException.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/InvalidTokenException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/MissingTokenException.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/constants.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/DB.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/requestConfig.php';

    abstract class Controller {

        // Configurações
        protected array $ALLOWED_METHODS;    // Métodos http permitidos
        protected array $AUTHORIZATION_MAP;  // Configuração de autenticação para métodos
        protected array $REQ_BODY_SPEC;      // Especificação do corpo dos pedidos, por método
        protected array $REQ_HEADER_SPEC;    // Especificação dos cabeçalhos dos pedidos, por método
        protected array $ALLOWED_URL_PARAMS; // Parametros de url permitidos

        protected array $URL_PARAMS;        // Constante dos parametros de URL
        protected array $REQ_BODY;          // Constante do corpo do pedido em ocorrência
        protected array $REQ_HEADERS;       // Constante dos cabeçalhos do pedido em ocorrência

        // Conecção com a BD
        protected PDO $pdo;

        /**
         * @param array $ALLOWED_METHODS Métodos http permitidos
         * @param array $AUTHORIZATION_MAP Configuração de autenticação, por método
         * @param array $REQ_BODY_SPEC Especificação do corpo dos pedidos, por método
         * @param array $REQ_HEADER_SPEC specificação dos cabeçalhos dos pedidos, por método
         */
        public function __construct(array $ALLOWED_METHODS,
                                    array $AUTHORIZATION_MAP,
                                    array $REQ_BODY_SPEC,
                                    array $REQ_HEADER_SPEC = array(),
                                    array $ALLOWED_URL_PARAMS = array())
        {
            $this->ALLOWED_METHODS = $ALLOWED_METHODS;
            $this->AUTHORIZATION_MAP = $AUTHORIZATION_MAP;
            $this->REQ_BODY_SPEC = $REQ_BODY_SPEC;
            $this->REQ_HEADER_SPEC = $REQ_HEADER_SPEC;
            $this->ALLOWED_URL_PARAMS = $ALLOWED_URL_PARAMS;
            // Obter corpo do pedido
            $this->REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();
            // Obter cabeçalhos do pedido
            $this->REQ_HEADERS = ControllerUtils::get_HTTP_request_headers();
            // Obter query params
            $components = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            $this->URL_PARAMS = array();
            parse_str($components, $this->URL_PARAMS);
        }

        /** Função de entrada para o tratamento de um pedido por um Controller
         * @return void
         */
        public function handleRequest() {
            // Configurar cabeçalhos, tratar do cors
            requestConfig();

            // Validar pedido, no nível de autorizações e formato
            try {
                self::validateRequest();
                // Iniciar conecção com a base de dados
                $this->pdo = DB::connect();

            } catch (InvalidTokenException) {
                notAuthrorizedResponse();
                return;
            } catch (MissingTokenException) {
                wrongFormatResponse("Falta token de autenticação");
                return;
            } catch (InvalidRequestBodyException) {
                wrongFormatResponse("Corpo do pedido mal formatado");
                return;
            } catch (MissingRequiredHeadersException) {
                wrongFormatResponse("Falta cabeçalhos da especificação do endpoint.");
                return;
            } catch (InvalidURLParamsException) {
                wrongFormatResponse("Parâmetros de URL não permitidos.");
                return;
            } catch (HttpRequestMethodNotAllowedException) {
                methodNotAvailable($_SERVER['REQUEST_METHOD']);
                return;
            } catch (DBConnectionException $e) {
                internalErrorResponse($e->getMessage());
                return;
            }

            // Continuar o tratamento do pedido, este método é definido nas subclasses.
            $this->routeRequest();
        }

        /** Função para validar pedidos http
         * @param
         * @return void
         * @throws MissingTokenException
         * @throws InvalidTokenException
         * @throws InvalidRequestBodyException
         * @throws MissingRequiredHeadersException
         * @throws HttpRequestMethodNotAllowedException
         * @throws InvalidURLParamsException
         */
        protected function validateRequest() {
            $REQ_METHOD = $_SERVER['REQUEST_METHOD'];

            // Verificar se o método http é permitido
            if (!in_array($REQ_METHOD, $this->ALLOWED_METHODS)) {
                throw new HttpRequestMethodNotAllowedException();
            }

            // Verificar se os cabeçalhos especificados estão definidos
            if (isset($this->REQ_HEADER_SPEC[$REQ_METHOD]) && !ControllerUtils::validateRequestHeaders($this->REQ_HEADERS, $this->REQ_HEADER_SPEC[$REQ_METHOD])) {
                throw new MissingRequiredHeadersException();
            }

            // Validar corpo do pedido
            if ($REQ_METHOD != GET && !ControllerUtils::validateRequestBody($this->REQ_BODY, $this->REQ_BODY_SPEC[$REQ_METHOD])) {
                throw new InvalidRequestBodyException();
            }

            // Validar URL params
            if (count($this->URL_PARAMS) > 0 && !ControllerUtils::validateURLParams($this->URL_PARAMS, $this->ALLOWED_URL_PARAMS)) {
                throw new InvalidURLParamsException();
            }

            // Fazer autenticação
            ControllerUtils::authorize($this->AUTHORIZATION_MAP, $this->REQ_HEADERS);
        }

        /** Função para fazer o meapeamento do tratamento do pedido consoante o seu método.
         */
        protected abstract function routeRequest();

    }