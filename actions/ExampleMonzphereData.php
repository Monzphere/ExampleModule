<?php
/**
 *  Controller de Dados - API Interativa
 *  Data Controller - Interactive API
 * 
 * Este arquivo processa requisições AJAX e retorna dados em formato JSON.
 * This file processes AJAX requests and returns data in JSON format.
 * É útil para criar interfaces dinâmicas e interativas.
 * It's useful for creating dynamic and interactive interfaces.
 * 
 * Funcionalidades:
 * Features:
 * - Busca detalhes de hosts com disponibilidade via icmpping
 * - Fetch host details with availability via icmpping
 * - Estatísticas gerais do sistema
 * - General system statistics
 * - Problemas atuais dos hosts
 * - Current host problems
 */

declare(strict_types = 1);

namespace Modules\ExampleMonzphere\Actions;

use CController;
use CControllerResponseData;
use API;
use Exception;

/**
 * Controller que fornece dados via AJAX para o frontend
 * Controller that provides data via AJAX to the frontend
 */
class ExampleMonzphereData extends CController 
{
    /**
     * Inicialização do controller
     * Controller initialization
     */
    protected function init(): void {
        // 🔓 Desabilitamos CSRF para facilitar requisições AJAX
        // 🔓 Disable CSRF to facilitate AJAX requests
        $this->disableCsrfValidation();
        
        // 📡 Configuramos cabeçalhos para resposta JSON
        // 📡 Configure headers for JSON response
        header('Content-Type: application/json');
        header('X-Content-Type-Options: nosniff');
    }

    /**
     * Verificação de permissões
     * Permission verification
     */
    protected function checkPermissions(): bool {
        // 👤 Usuários normais do Zabbix podem acessar
        // 👤 Normal Zabbix users can access
        return $this->getUserType() >= USER_TYPE_ZABBIX_USER;
    }

    /**
     * Validação dos parâmetros de entrada
     * Input parameter validation
     */
    protected function checkInput(): bool {
        //  Definimos os campos que aceitamos
        //  Define the fields we accept
        $fields = [
            'action_param' => 'string',    // Ação a executar / Action to execute
            'hostid' => 'id',              // ID do host (opcional) / Host ID (optional)
            'search' => 'string'           // Termo de busca (opcional) / Search term (optional)
        ];

        $ret = $this->validateInput($fields);

        if (!$ret) {
            //  Se validação falhar, retornamos erro JSON
            //  If validation fails, return JSON error
            echo json_encode([
                'success' => false,
                'error' => _('Parâmetros inválidos!')  // Traduzível / Translatable
            ]);
            exit();
        }

        return $ret;
    }

    /**
     *  Busca detalhes de um host específico
     *  Fetch details of a specific host
     */
    private function getHostDetails(string $hostid): array {
        try {
            // Buscamos informações detalhadas do host
            // Fetch detailed host information
            $hosts = API::Host()->get([
                'output' => ['hostid', 'name', 'status'],
                'selectInterfaces' => ['ip', 'dns', 'port', 'type'],
                'selectItems' => ['itemid', 'name', 'lastvalue', 'units', 'key_'],
                'hostids' => [$hostid],
                'limit' => 1
            ]);

            if (empty($hosts)) {
                return ['error' => _('Host não encontrado!')];  // Traduzível / Translatable
            }

            $host = $hosts[0];

            // Buscar disponibilidade através do item icmpping
            // Search for availability through icmpping item
            $availability = 'unknown';
            $icmpping_value = null;
            
            if (!empty($host['items'])) {
                foreach ($host['items'] as $item) {
                    if ($item['key_'] === 'icmpping') {
                        $icmpping_value = $item['lastvalue'];
                        $availability = ($icmpping_value == 1) ? 'available' : 'unavailable';
                        break;
                    }
                }
            }
            
            // Se não encontrou icmpping, tentar buscar especificamente
            // If icmpping not found, try to search specifically
            if ($availability === 'unknown') {
                try {
                    $icmpping_items = API::Item()->get([
                        'output' => ['itemid', 'lastvalue'],
                        'hostids' => [$hostid],
                        'filter' => ['key_' => 'icmpping'],
                        'limit' => 1
                    ]);
                    
                    if (!empty($icmpping_items)) {
                        $icmpping_value = $icmpping_items[0]['lastvalue'];
                        $availability = ($icmpping_value == 1) ? 'available' : 'unavailable';
                    }
                } catch (Exception $e) {
                    // Se não conseguir buscar icmpping, manter como unknown
                    // If can't fetch icmpping, keep as unknown
                }
            }

            // Buscamos problemas atuais do host
            // Fetch current host problems
            $problems = API::Problem()->get([
                'output' => ['eventid', 'name', 'severity'],
                'hostids' => [$hostid],
                'recent' => true,
                'limit' => 5
            ]);

            return [
                'host' => $host,
                'problems' => $problems ?: [],
                'total_problems' => count($problems ?: []),
                'availability' => $availability,
                'icmpping_value' => $icmpping_value
            ];

        } catch (Exception $e) {
            return ['error' => _('Erro ao buscar dados: ') . $e->getMessage()];  // Traduzível / Translatable
        }
    }

    /**
     *  Gera estatísticas gerais do Zabbix
     *  Generate general Zabbix statistics
     */
    private function getGeneralStats(): array {
        try {
            // Contamos recursos do Zabbix com tratamento de erro individual
            // Count Zabbix resources with individual error handling
            $stats = [];
            
            // Hosts
            try {
                $stats['hosts'] = API::Host()->get(['countOutput' => true]);
            } catch (Exception $e) {
                $stats['hosts'] = 0;
            }
            
            // Items
            try {
                $stats['items'] = API::Item()->get([
                    'countOutput' => true,
                    'webitems' => false
                ]);
            } catch (Exception $e) {
                $stats['items'] = 0;
            }
            
            // Triggers
            try {
                $stats['triggers'] = API::Trigger()->get([
                    'countOutput' => true,
                    'filter' => ['flags' => ZBX_FLAG_DISCOVERY_NORMAL]
                ]);
            } catch (Exception $e) {
                $stats['triggers'] = 0;
            }
            
            // Problems
            try {
                $stats['problems'] = API::Problem()->get([
                    'countOutput' => true, 
                    'recent' => true
                ]);
            } catch (Exception $e) {
                $stats['problems'] = 0;
            }

            return [
                'stats' => $stats
            ];

        } catch (Exception $e) {
            return ['error' => _('Erro ao obter estatísticas: ') . $e->getMessage()];  // Traduzível / Translatable
        }
    }

    /**
     * Ação principal - processa a requisição
     * Main action - processes the request
     */
    protected function doAction(): void {
        try {
            //  Determinamos qual ação executar
            //  Determine which action to execute
            $action = $this->getInput('action_param', 'stats');
            
            switch ($action) {
                case 'host_details':
                    $hostid = $this->getInput('hostid');
                    if ($hostid) {
                        $result = $this->getHostDetails($hostid);
                    } else {
                        $result = ['error' => _('ID do host é obrigatório!')];  // Traduzível / Translatable
                    }
                    break;

                case 'stats':
                default:
                    $result = $this->getGeneralStats();
                    break;
            }

            //  Adicionamos flag de sucesso se não houver erro
            //  Add success flag if there's no error
            if (!isset($result['error'])) {
                $result['success'] = true;
            } else {
                $result['success'] = false;
            }

            //  Retornamos resposta JSON
            //  Return JSON response
            echo json_encode($result);

        } catch (Exception $e) {
            // 🚨 Tratamento de erro geral
            // 🚨 General error handling
            echo json_encode([
                'success' => false,
                'error' => _('Erro interno: ') . $e->getMessage()  // Traduzível / Translatable
            ]);
        }
        
        exit();
    }
} 