<?php
/**
 * 🎯 Controller da View - Exemplo Educativo
 * 🎯 View Controller - Educational Example
 * 
 * Este arquivo controla a exibição da nossa página.
 * This file controls the display of our page.
 * É aqui que preparamos os dados que serão mostrados na tela.
 * This is where we prepare the data that will be displayed on screen.
 */

namespace Modules\ExampleMonzphere\Actions;

use CControllerResponseData;
use CController;
use API;
use Exception;

/**
 * Controller responsável por exibir a página principal do módulo
 * Controller responsible for displaying the main module page
 */
class ExampleMonzphereView extends CController
{
    /**
     * Inicialização do controller
     * Controller initialization
     * Aqui configuramos as opções básicas
     * Here we configure the basic options
     */
    public function init(): void {
        // 🔓 Desabilitamos a validação CSRF para simplificar o exemplo
        // 🔓 Disable CSRF validation to simplify the example
        $this->disableCsrfValidation();
    }
    
    /**
     * Validação dos dados de entrada
     * Input data validation
     * Aqui verificamos se os dados recebidos estão corretos
     * Here we check if the received data is correct
     */
    protected function checkInput(): bool {
        // ✅ Para este exemplo simples, sempre retornamos true
        // ✅ For this simple example, we always return true
        return true;
    }
    
    /**
     * Verificação de permissões
     * Permission verification
     * Aqui definimos quem pode acessar o módulo
     * Here we define who can access the module
     */
    protected function checkPermissions(): bool {
        //  Permite acesso para usuários do Zabbix (nível USER ou superior)
        //  Allow access for Zabbix users (USER level or higher)
        return $this->getUserType() >= USER_TYPE_ZABBIX_USER;
    }

    /**
     * Ação principal - prepara os dados para a view
     * Main action - prepares data for the view
     * Este é o coração do controller, onde buscamos dados da API
     * This is the heart of the controller, where we fetch data from the API
     */
    protected function doAction(): void
    {
        try {
            //  Buscamos alguns hosts para mostrar no exemplo
            //  Fetch some hosts to show in the example
            // A API do Zabbix nos permite buscar qualquer informação
            // The Zabbix API allows us to fetch any information
            $hosts = API::Host()->get([
                'output' => ['hostid', 'name', 'status'],  // Campos que queremos / Fields we want
                'sortfield' => 'name',                      // Ordenar por nome / Sort by name
                'sortorder' => ZBX_SORT_UP,                // Ordem crescente / Ascending order
                'limit' => 10                              // Limitar a 10 hosts / Limit to 10 hosts
            ]);

            //  Contamos quantos problemas existem no momento
            //  Count how many problems exist at the moment
            $problems = API::Problem()->get([
                'output' => ['eventid', 'name', 'severity'],
                'recent' => true,   // Apenas problemas atuais / Only current problems
                'limit' => 5       // Limitar a 5 problemas / Limit to 5 problems
            ]);

            //  Preparamos os dados para enviar para a view
            //  Prepare data to send to the view
            $data = [
                'hosts' => $hosts ?: [],                    // Lista de hosts (ou array vazio) / Host list (or empty array)
                'problems' => $problems ?: [],              // Lista de problemas / Problem list
                'total_hosts' => count($hosts ?: []),       // Total de hosts / Total hosts
                'total_problems' => count($problems ?: []), // Total de problemas / Total problems
                'page_title' => _('🎯 Example Monzphere - Tutorial Interativo'),  // Título da página / Page title
                'welcome_message' => _('Bem-vindo ao tutorial de módulos Zabbix!')  // Mensagem de boas-vindas / Welcome message
            ];

        } catch (Exception $e) {
            // 🚨 Se houver erro, preparamos dados de fallback
            // 🚨 If there's an error, prepare fallback data
            $data = [
                'hosts' => [],
                'problems' => [],
                'total_hosts' => 0,
                'total_problems' => 0,
                'page_title' => _('🎯 Example Monzphere - Tutorial Interativo'),
                'welcome_message' => _('Bem-vindo ao tutorial de módulos Zabbix!'),
                'error' => _('Erro ao carregar dados: ') . $e->getMessage()  // Erro traduzível / Translatable error
            ];
        }

        //  Enviamos os dados para a view
        //  Send data to the view
        $response = new CControllerResponseData($data);
        $this->setResponse($response);
    }
}