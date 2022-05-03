<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/ControllerUtils.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/ControllerUtils.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/InvalidTokenException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/MissingTokenException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/MissingRequiredHeadersException.php';

    abstract class Controller {

        protected array $REQ_BODY;
        protected array $REQ_HEADERS;
        protected array $AUTHORIZATION_MAP;
        protected array $REQ_BODY_SPEC;
        protected array $REQ_HEADER_SPEC;

        public function __construct(array $AUTHORIZATION_MAP, array $REQ_BODY_SPEC, array $REQ_HEADER_SPEC)
        {
            $this->AUTHORIZATION_MAP = $AUTHORIZATION_MAP;
            $this->REQ_BODY_SPEC = $REQ_BODY_SPEC;
            $this->REQ_HEADER_SPEC = $REQ_HEADER_SPEC;
            $this->REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();
            $this->REQ_HEADERS = ControllerUtils::get_HTTP_request_headers();
        }

        /**
         * Função de entrada para o tratamento de um pedido por um Controller
         * @return void
         */
        public function handleRequest() {
            // Configurar cabeçalhos, tratar do cors
            requestConfig();

            // Validar pedido, no nível de autorizações e formato
            try {
                self::validateRequest();
            } catch (InvalidTokenException) {
                notAuthrorizedResponse();
                return;
            } catch (MissingTokenException) {
                wrongFormatResponse("Falta token de autneticação");
                return;
            } catch (InvalidRequestBodyException) {
                wrongFormatResponse("Corpo do pedido mal formatado");
                return;
            } catch (MissingRequiredHeadersException) {
                wrongFormatResponse("Falta cabeçalhos da especificação do endpoint.");
            }

            // Continuar o tratamento do pedido
            $this->routeRequest();
        }

        /**
         * Função para validar pedidos
         * @param
         * @return void
         * @throws MissingTokenException
         * @throws InvalidTokenException
         * @throws InvalidRequestBodyException
         * @throws MissingRequiredHeadersException
         */
        protected function validateRequest() {
            $REQ_METHOD = $_SERVER['REQUEST_METHOD'];

            // Validar corpo do pedido
            if ($REQ_METHOD != GET && !ControllerUtils::validateRequestBody($this->REQ_BODY, $this->REQ_BODY_SPEC[$REQ_METHOD])) {
                throw new InvalidRequestBodyException();
            }

            // Verificar se os cabeçalhos especificados estão definidos
            if (!ControllerUtils::validateRequestHeaders($this->REQ_HEADERS, $this->REQ_HEADER_SPEC[$REQ_METHOD])) {
                throw new MissingRequiredHeadersException();
            }

            // Fazer autenticação
            ControllerUtils::authorize($this->AUTHORIZATION_MAP, $this->REQ_HEADERS);
        }

        /**
         * Função para fazer o meapeamento do tratamento do pedido consoante o seu método.
         */
        protected abstract function routeRequest();
    }

