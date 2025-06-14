<?php
/**
 * 🚀 Módulo Example Monzphere - Tutorial Prático de Módulos Zabbix
 * 🚀 Example Monzphere Module - Practical Zabbix Module Tutorial
 * 
 * Este é um exemplo simples e educativo de como criar módulos para o Zabbix.
 * This is a simple and educational example of how to create Zabbix modules.
 * Aqui você aprenderá os conceitos básicos de desenvolvimento de módulos.
 * Here you will learn the basic concepts of module development.
 * 
 * @author Monzphere
 * @version 1.0
 */

namespace Modules\ExampleMonzphere;

use Zabbix\Core\CModule;
use APP;
use CMenuItem;

/**
 * Classe principal do módulo
 * Main module class
 * Esta classe é o ponto de entrada do seu módulo no Zabbix
 * This class is the entry point of your module in Zabbix
 */
class Module extends CModule 
{
    /**
     * Método init() - Aqui configuramos o módulo
     * init() method - Here we configure the module
     * Este método é chamado quando o Zabbix carrega o módulo
     * This method is called when Zabbix loads the module
     */
    public function init(): void {
        // 📋 Adicionamos um item ao menu principal do Zabbix
        // 📋 Add an item to the main Zabbix menu
        // Isto criará uma nova opção no menu para acessar nosso módulo
        // This will create a new menu option to access our module
        APP::Component()->get('menu.main')
            ->findOrAdd(_('Monitoring'))  // Encontra ou cria o menu "Monitoring" / Find or create "Monitoring" menu
                ->getSubmenu()            // Pega o submenu / Get the submenu
                    ->insertAfter(_('Discovery'), // Insere após "Discovery" / Insert after "Discovery"
                        (new CMenuItem(_('🎯 Example Monzphere')))  // Cria novo item com emoji / Create new item with emoji
                            ->setAction('examplemonzphere.view')    // Define a ação (rota) / Set action (route)
                    );
    }
}