<?php
/**
 *  View Template - Interface do Usuário
 *  View Template - User Interface
 * 
 * Este arquivo define como nossa página será exibida usando as classes PHP do Zabbix.
 * This file defines how our page will be displayed using Zabbix PHP classes.
 */

//  Extrair dados passados pelo controller
//  Extract data passed by the controller
$hosts = $data['hosts'];
$problems = $data['problems'];
$total_hosts = $data['total_hosts'];
$total_problems = $data['total_problems'];
$page_title = $data['page_title'];
$welcome_message = $data['welcome_message'];

//  Criar página principal
//  Create main page
$page = (new CHtmlPage())
    ->setTitle($page_title);

//  Container principal compacto
//  Main compact container
$main_container = new CDiv();
$main_container->addClass('examplemonzphere-container');



$title = new CTag('h2', true, _('🎯 Example Monzphere'));  // Traduzível / Translatable
$subtitle = new CTag('span', true, _('Tutorial Prático de Módulos Zabbix'));  // Traduzível / Translatable
$subtitle->addClass('subtitle');

$fun_gif = new CTag('img', true, '', [
    'src' => 'https://media.giphy.com/media/26tn33aiTi1jkl6H6/giphy.gif',
    'alt' => _('Coding GIF'),  // Traduzível / Translatable
    'class' => 'fun-gif',
    'id' => 'main-gif'
]);


//  Layout em duas colunas
//  Two-column layout
$two_column = new CDiv();
$two_column->addClass('two-column-layout');

//  Coluna esquerda - Estatísticas + Busca
//  Left column - Statistics + Search
$left_column = new CDiv();
$left_column->addClass('left-column');

// Stats compactos / Compact stats
$stats_section = new CDiv();
$stats_section->addClass('compact-stats');
$stats_section->addItem(new CTag('h3', true, _('📈 Estatísticas')));  // Traduzível / Translatable

$stats_grid = new CDiv();
$stats_grid->addClass('stats-grid-compact');

$stat_cards = [
    ['total-hosts', $total_hosts, _('Hosts')],      // Traduzível / Translatable
    ['total-problems', $total_problems, _('Problemas')],  // Traduzível / Translatable
    ['total-items', '-', _('Items')],               // Traduzível / Translatable
    ['total-triggers', '-', _('Triggers')]          // Traduzível / Translatable
];

foreach ($stat_cards as $card) {
    $stat_card = new CDiv();
    $stat_card->addClass('stat-card-compact');
    
    $stat_number = new CDiv($card[1]);
    $stat_number->addClass('stat-number-compact');
    $stat_number->setId($card[0]);
    
    $stat_label = new CDiv($card[2]);
    $stat_label->addClass('stat-label-compact');
    
    $stat_card->addItem($stat_number);
    $stat_card->addItem($stat_label);
    $stats_grid->addItem($stat_card);
}

$stats_section->addItem($stats_grid);

$load_btn = new CButton('load-stats', _('🔄 Atualizar'));  // Traduzível / Translatable
$load_btn->addClass('btn-alt btn-compact');
$load_btn->setId('load-stats');
$stats_section->addItem($load_btn);

// GIF dinâmico baseado em valores
// Dynamic GIF based on values
$dynamic_gif = new CDiv();
$dynamic_gif->setId('dynamic-gif-container');
$dynamic_gif->addClass('dynamic-gif-container');
$stats_section->addItem($dynamic_gif);

$left_column->addItem($stats_section);

// Busca compacta - USANDO MÉTODO SEARCH NATIVO
// Compact search - USING NATIVE SEARCH METHOD
$search_section = new CDiv();
$search_section->addClass('compact-search');
$search_section->addItem(new CTag('h3', true, _('🔍 Buscar Hosts')));  // Traduzível / Translatable

$search_form = new CDiv();
$search_form->addClass('search-form');

//  Campo de busca nativo para hosts
//  Native search field for hosts
$search_input = new CTextBox('host-search', '');
$search_input->setId('host-search');
$search_input->setAttribute('placeholder', _('Nome do host...'));  // Traduzível / Translatable
$search_input->setAttribute('maxlength', '255');

$search_btn = new CButton('search-btn', _('Buscar'));  // Traduzível / Translatable
$search_btn->addClass('btn-link btn-compact');
$search_btn->setId('search-btn');

$search_form->addItem($search_input);
$search_form->addItem($search_btn);
$search_section->addItem($search_form);

$search_results = new CDiv();
$search_results->setId('search-results');
$search_results->addClass('search-results-compact');
$search_section->addItem($search_results);

$left_column->addItem($search_section);

$two_column->addItem($left_column);

//  Coluna direita - Hosts + Problemas
//  Right column - Hosts + Problems
$right_column = new CDiv();
$right_column->addClass('right-column');

// Hosts compactos / Compact hosts
$hosts_section = new CDiv();
$hosts_section->addClass('compact-hosts');
$hosts_section->addItem(new CTag('h3', true, _('🏠 Hosts (Top 6)')));  // Traduzível / Translatable

if (!empty($hosts)) {
    $hosts_grid = new CDiv();
    $hosts_grid->addClass('hosts-grid-compact');
    
    $limited_hosts = array_slice($hosts, 0, 6); // Máximo 6 hosts / Maximum 6 hosts
    foreach ($limited_hosts as $host) {
        $host_card = new CDiv();
        $host_card->addClass('host-card-compact');
        $host_card->setAttribute('data-hostid', $host['hostid']);
        
        $host_name = new CDiv(htmlspecialchars(substr($host['name'], 0, 20) . (strlen($host['name']) > 20 ? '...' : '')));
        $host_name->addClass('host-name-compact');
        
        $status_icon = $host['status'] == 0 ? '✅' : '❌';
        $host_status = new CSpan($status_icon);
        $host_status->addClass('host-status-compact');
        
        $details_btn = new CButton('details', '👁️');
        $details_btn->addClass('host-details-btn btn-compact');
        $details_btn->setAttribute('data-hostid', $host['hostid']);
        $details_btn->setAttribute('title', _('Ver detalhes de') . ' ' . htmlspecialchars($host['name']));  // Traduzível / Translatable
        
        $host_card->addItem($host_name);
        $host_card->addItem($host_status);
        $host_card->addItem($details_btn);
        
        $hosts_grid->addItem($host_card);
    }
    
    $hosts_section->addItem($hosts_grid);
} else {
    $no_data = new CDiv(_('😔 Nenhum host'));  // Traduzível / Translatable
    $no_data->addClass('no-data-compact');
    $hosts_section->addItem($no_data);
}

$right_column->addItem($hosts_section);

// Problemas compactos / Compact problems
$problems_section = new CDiv();
$problems_section->addClass('compact-problems');
$problems_section->addItem(new CTag('h3', true, _('🚨 Problemas (Top 3)')));  // Traduzível / Translatable

if (!empty($problems)) {
    $problems_list = new CDiv();
    $problems_list->addClass('problems-list-compact');
    
    $limited_problems = array_slice($problems, 0, 3); // Máximo 3 problemas / Maximum 3 problems
    foreach ($limited_problems as $problem) {
        $problem_item = new CDiv();
        $problem_item->addClass('problem-item-compact severity-' . $problem['severity']);
        
        $problem_text = htmlspecialchars(substr($problem['name'], 0, 40) . (strlen($problem['name']) > 40 ? '...' : ''));
        $problem_name = new CDiv($problem_text);
        $problem_name->addClass('problem-name-compact');
        
        $severity_badge = new CSpan('S' . $problem['severity']);
        $severity_badge->addClass('severity-badge');
        
        $problem_item->addItem($problem_name);
        $problem_item->addItem($severity_badge);
        $problems_list->addItem($problem_item);
    }
    
    $problems_section->addItem($problems_list);
} else {
    $no_problems = new CDiv(_('🎉 Nenhum problema!'));  // Traduzível / Translatable
    $no_problems->addClass('no-data-compact');
    $problems_section->addItem($no_problems);
}

$right_column->addItem($problems_section);

$two_column->addItem($right_column);
$main_container->addItem($two_column);

// 📱 Modal compacto / Compact modal
$modal = new CDiv();
$modal->setId('host-modal');
$modal->addClass('modal-compact');

$modal_content = new CDiv();
$modal_content->addClass('modal-content-compact');

$modal_header = new CDiv();
$modal_header->addClass('modal-header-compact');
$modal_header->addItem(new CTag('h4', true, _('📋 Detalhes do Host')));  // Traduzível / Translatable

$close_btn = new CSpan('×');
$close_btn->addClass('close');
$modal_header->addItem($close_btn);

$modal_body = new CDiv();
$modal_body->setId('host-details-content');
$modal_body->addClass('modal-body-compact');

$modal_content->addItem($modal_header);
$modal_content->addItem($modal_body);
$modal->addItem($modal_content);

$main_container->addItem($modal);

//  Seção educativa com código - ADICIONADA DE VOLTA
//  Educational section with code - ADDED BACK
$tutorial_section = new CDiv();
$tutorial_section->addClass('tutorial-section-compact');
$tutorial_section->addItem(new CTag('h3', true, _('📚 O que este módulo demonstra:')));  // Traduzível / Translatable

$tutorial_list = new CList();
$tutorial_list->addClass('tutorial-list-compact');

$tutorial_items = [
    _('🏗️ Estrutura de Módulo: Como organizar arquivos PHP, CSS e JS'),      // Traduzível / Translatable
    _('🔗 API do Zabbix: Como usar API::Host(), API::Problem(), etc.'),       // Traduzível / Translatable
    _('📊 Controllers: Como criar Views e Data controllers'),                 // Traduzível / Translatable
    _('🎨 Frontend: Como criar interfaces com classes do Zabbix'),            // Traduzível / Translatable
    _('📡 AJAX: Como fazer requisições dinâmicas'),                           // Traduzível / Translatable
    _('🔍 Busca de Hosts: Como usar JSON-RPC method "search"'),               // Traduzível / Translatable
    _('📊 Monitoramento: Disponibilidade via item key "icmpping"'),           // Traduzível / Translatable
    _('🎬 GIFs Dinâmicos: Animações baseadas em valores das métricas'),       // Traduzível / Translatable
    _('📝 Manifest: Como configurar o arquivo manifest.json')                 // Traduzível / Translatable
];

foreach ($tutorial_items as $item) {
    $tutorial_list->addItem(new CListItem($item));
}

$tutorial_section->addItem($tutorial_list);

// Exemplo de código - MÉTODO SEARCH PARA HOSTS
//  Code example - SEARCH METHOD FOR HOSTS
$code_example = new CDiv();
$code_example->addClass('code-example-compact');
$code_example->addItem(new CTag('h4', true, _('💻 Exemplo de uso da API:')));  // Traduzível / Translatable

$code_pre = new CTag('pre', true, '// Buscar hosts via PHP API
$hosts = API::Host()->get([
    \'output\' => [\'hostid\', \'name\', \'status\'],
    \'selectItems\' => [\'itemid\', \'key_\', \'lastvalue\'],
    \'limit\' => 10
]);

// Verificar disponibilidade via icmpping
foreach ($hosts as &$host) {
    $availability = \'unknown\';
    foreach ($host[\'items\'] as $item) {
        if ($item[\'key_\'] === \'icmpping\') {
            $availability = ($item[\'lastvalue\'] == 1) ? 
                \'available\' : \'unavailable\';
            break;
        }
    }
    $host[\'availability\'] = $availability;
}

// JavaScript - GIFs dinâmicos baseados em valores
function checkForSpecialGif(stats) {
    const problems = parseInt(stats.problems) || 0;
    const hosts = parseInt(stats.hosts) || 0;
    
    if (problems >= 3) {
        showGif({
            url: "fire.gif",
            text: "🔥 SISTEMA EM CHAMAS!",
            type: "fire"
        });
    } else if (hosts >= 10) {
        showGif({
            url: "rocket.gif", 
            text: "🚀 INFRAESTRUTURA MASSIVA!",
            type: "rocket"
        });
    } else if (problems === 0) {
        showGif({
            url: "celebrate.gif",
            text: "🎉 TUDO TRANQUILO!",
            type: "celebrate"
        });
    }
}

// Busca de hosts JSON-RPC (JavaScript)
const searchPayload = {
    jsonrpc: "2.0",
    method: "search",
    params: {
        search: "nome_host"
    },
    id: 1
};

fetch(\'/jsrpc.php?output=json-rpc\', {
    method: \'POST\',
    headers: { \'Content-Type\': \'application/json\' },
    body: JSON.stringify(searchPayload)
});');

$code_example->addItem($code_pre);
$tutorial_section->addItem($code_example);

$main_container->addItem($tutorial_section);

//  Footer compacto
//  Compact footer
$footer_section = new CDiv();
$footer_section->addClass('footer-compact');
$footer_section->addItem(new CTag('p', true, _('🚀 Example Monzphere - Tutorial prático | 👨‍💻 Monzphere ') . date('Y')));  // Traduzível / Translatable

$main_container->addItem($footer_section);

// Adicionar container à página
// Add container to page
$page->addItem($main_container);

// 📺 Exibir a página
// 📺 Display the page
$page->show(); 