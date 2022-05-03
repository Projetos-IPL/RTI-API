<?php

    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/InvalidTokenException.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/utils/exceptions/MissingTokenException.php';

    abstract class Controller {

        protected array $REQ_BODY;
        protected array $AUTHORIZATION_MAP;
        protected array $REQ_BODY_SPEC;

        public function __construct(array $AUTHORIZATION_MAP, array $REQ_BODY_SPEC)
        {
            $this->AUTHORIZATION_MAP = $AUTHORIZATION_MAP;
            $this->REQ_BODY_SPEC = $REQ_BODY_SPEC;
            $this->REQ_BODY = json_decode(file_get_contents('php://input'), true) ?: array();
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
                self::validateRequest($_SERVER['REQUEST_METHOD']);

                if (!self::validateRequestBody()) {
                    wrongFormatResponse();
                    return;
                }

            } catch (InvalidTokenException) {
                notAuthrorizedResponse();
                return;
            } catch (MissingTokenException) {
                wrongFormatResponse('Falta token de autenticação');
                return;
            }

            // Continuar o tratamento do pedido
            $this->routeRequest();
        }

        /**
         * Função para validar pedidos
         * @param $REQ_METHOD string Método a ser verificado no AUTHORIZATION_MAP
         * @return void
         * @throws MissingTokenException
         * @throws InvalidTokenException
         */
        protected function validateRequest(string $REQ_METHOD) {
            // Métodos não especificados no AUTHORIZATION_MAP são considerados privados, logo são verificados
            if (!isset($this->AUTHORIZATION_MAP[$REQ_METHOD]) || !$this->AUTHORIZATION_MAP[$REQ_METHOD]) {
                if (!isset($this->REQ_BODY['token'])) {
                    throw new MissingTokenException();
                }
                if (!AuthUtils::verifyJWT($this->REQ_BODY['token'])) {
                    throw new InvalidTokenException();
                }
            }
        }

        /**
         * Função para validar o corpo de um pedido
         * @param array $reqBody Corpo do pedido
         * @param array $reqBodySpecification Especificação do corpo do pedido
         * @return bool Verdadeiro se o corpo do pedido respeitar a especificação, falso se não
         */
        protected function validateRequestBody() : bool
        {
            $valid = true;
            foreach ($this->REQ_BODY_SPEC[$_SERVER['REQUEST_METHOD']] as $param) {
                if (!isset($this->REQ_BODY[$param])) {
                    $valid = false;
                }
            }
            if (count($this->REQ_BODY) != count($this->REQ_BODY_SPEC)) {
                $valid = false;
            }
            return $valid;
        }

        /**
         * Função abstrata para fazer o meapeamento do tratamento do pedido consoante o seu método.
         */
        protected abstract function routeRequest();



    }

