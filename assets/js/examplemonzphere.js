/**
 * 🚀 JavaScript do Example Monzphere - Funcionalidades Interativas
 * 🚀 Example Monzphere JavaScript - Interactive Features
 * 
 * Este arquivo contém toda a lógica JavaScript para tornar o módulo interativo.
 * This file contains all JavaScript logic to make the module interactive.
 * Demonstra como fazer requisições AJAX e manipular a DOM.
 * Demonstrates how to make AJAX requests and manipulate the DOM.
 * 
 * 🎬 Sistema de GIFs Dinâmicos:
 * 🎬 Dynamic GIFs System:
 * - GIF principal no cabeçalho com animação
 * - Main header GIF with animation
 * - GIFs que aparecem baseados nos valores das estatísticas:
 * - GIFs that appear based on statistics values:
 *   • 🔥 Problemas >= 3: GIF de fogo
 *   • 🔥 Problems >= 3: Fire GIF
 *   • 🚀 Hosts >= 10: GIF de foguete  
 *   • 🚀 Hosts >= 10: Rocket GIF
 *   • 📊 Items >= 100: GIF de dados
 *   • 📊 Items >= 100: Data GIF
 *   • ⚡ Triggers >= 20: GIF de raio
 *   • ⚡ Triggers >= 20: Lightning GIF
 *   • 🎉 Sem problemas: GIF de celebração
 *   • 🎉 No problems: Celebration GIF
 * 
 * @author Monzphere
 * @version 1.0
 */

// 🌟 Inicialização quando DOM carrega
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Example Monzphere carregado! / Example Monzphere loaded!');

    // 🔧 URLs e configurações
    // 🔧 URLs and configurations
    const API_URL = 'zabbix.php?action=examplemonzphere.data';
    const DEBUG_MODE = true; // Altere para false para desativar logs / Change to false to disable logs
    
    // 📊 Função para carregar estatísticas
    // 📊 Function to load statistics
    function loadStats() {
        console.log('📈 Carregando estatísticas... / Loading statistics...');
        
        // Visual feedback
        const loadBtn = document.getElementById('load-stats');
        if (loadBtn) {
            loadBtn.textContent = '⏳ Carregando... / Loading...';
            loadBtn.disabled = true;
        }
        
        fetch(API_URL + '&action_param=stats', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 Estatísticas recebidas:', data);
            
            if (data.success && data.stats) {
                // Atualizar hosts
                updateStatElement('total-hosts', data.stats.hosts || 0);
                
                // Atualizar problemas
                updateStatElement('total-problems', data.stats.problems || 0);
                
                // Atualizar items - CORRIGIDO
                updateStatElement('total-items', data.stats.items || 0);
                
                // Atualizar triggers - CORRIGIDO
                updateStatElement('total-triggers', data.stats.triggers || 0);
                
                // 🔥 Verificar se deve mostrar GIF especial baseado nos valores
                checkForSpecialGif(data.stats);
                
                // 🎭 Animar GIF principal
                animateMainGif();
                
                console.log('✅ Estatísticas atualizadas com sucesso! / Statistics updated successfully!');
            } else {
                console.error('❌ Erro nos dados: / Error in data:', data.error || 'Dados inválidos / Invalid data');
                showError('Erro ao carregar estatísticas: / Error loading statistics: ' + (data.error || 'Dados inválidos / Invalid data'));
            }
        })
        .catch(error => {
            console.error('❌ Erro ao carregar estatísticas: / Error loading statistics:', error);
            showError('Erro de conexão ao carregar estatísticas / Connection error loading statistics');
        })
        .finally(() => {
            // Restaurar botão
            // Restore button
            if (loadBtn) {
                loadBtn.textContent = '🔄 Atualizar / Update';
                loadBtn.disabled = false;
            }
        });
    }
    
    // 🔧 Função auxiliar para atualizar elementos de estatística
    // 🔧 Helper function to update statistics elements
    function updateStatElement(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            // Animação simples
            // Simple animation
            element.style.opacity = '0.5';
            setTimeout(() => {
                element.textContent = value.toLocaleString();
                element.style.opacity = '1';
            }, 200);
        }
    }
    
    // 🔍 Função de busca de hosts - USANDO MÉTODO NATIVO DO ZABBIX
    // 🔍 Host search function - USING NATIVE ZABBIX METHOD
    function searchHosts() {
        const searchInput = document.getElementById('host-search');
        const searchResults = document.getElementById('search-results');
        
        if (!searchInput || !searchResults) {
            console.error('❌ Elementos de busca não encontrados / Search elements not found');
            return;
        }
        
        const searchTerm = searchInput.value.trim();
        
        if (searchTerm.length < 2) {
            searchResults.innerHTML = '<div class="no-results">Digite pelo menos 2 caracteres / Type at least 2 characters</div>';
            return;
        }
        
        console.log('🔍 Buscando hosts com método nativo: / Searching hosts with native method:', searchTerm);
        
        // Visual feedback
        searchResults.innerHTML = '<div class="no-results">🔍 Buscando hosts... / Searching hosts...</div>';
        
        // 🚀 Usando JSON-RPC nativo do Zabbix para busca de hosts
        // 🚀 Using native Zabbix JSON-RPC for host search
        const searchPayload = {
            jsonrpc: "2.0",
            method: "search",
            params: {
                search: searchTerm
            },
            id: Math.floor(Math.random() * 1000) + 1
        };
        
        fetch('/jsrpc.php?output=json-rpc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(searchPayload)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('🔍 Resultados da busca de hosts: / Host search results:', data);
            
            if (data.result) {
                displayHostSearchResults(data.result, searchResults);
            } else if (data.error) {
                searchResults.innerHTML = '<div class="no-results">❌ Erro: ' + data.error.message + '</div>';
            } else {
                searchResults.innerHTML = '<div class="no-results">❌ Resposta inválida</div>';
            }
        })
        .catch(error => {
            console.error('❌ Erro na busca de hosts: / Error in host search:', error);
            searchResults.innerHTML = '<div class="no-results">❌ Erro de conexão</div>';
        });
    }
    
    //  Função para exibir resultados da busca de hosts
    //  Function to display host search results
    function displayHostSearchResults(results, container) {
        if (!results || results.length === 0) {
            container.innerHTML = '<div class="no-results">😔 Nenhum host encontrado / No hosts found</div>';
            return;
        }
        
        if (DEBUG_MODE) console.log('🔍 Dados da busca de hosts / Host search data:', results);
        
        let html = '';
        
        // Processar resultados como hosts
        // Process results as hosts
        results.slice(0, 8).forEach(item => {
            if (DEBUG_MODE) console.log('🏠 Host individual / Individual host:', item);
            
            // Extrair informações do host
            // Extract host information
            const hostName = item.name || item.label || 'Host sem nome / Unnamed host';
            const hostId = item.id || item.hostid || '';
            
            html += `
                <div class="search-result-item host-result" data-hostid="${hostId}">
                    <span>🏠 ${hostName}</span>
                    <span class="result-type">HOST</span>
                </div>
            `;
        });
        
        if (html === '') {
            container.innerHTML = '<div class="no-results">😔 Nenhum host encontrado / No hosts found</div>';
            return;
        }
        
        container.innerHTML = html;
        
        // Adicionar event listeners para os resultados
        // Add event listeners for results
        container.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const hostId = this.getAttribute('data-hostid');
                if (hostId) {
                    showHostDetails(hostId);
                } else {
                    console.warn('⚠️ ID do host não encontrado / Host ID not found');
                }
            });
        });
    }
    
    // 👁️ Função para mostrar detalhes do host
    function showHostDetails(hostid) {
        console.log('👁️ Mostrando detalhes do host:', hostid);
        
        const modal = document.getElementById('host-modal');
        const modalContent = document.getElementById('host-details-content');
        
        if (!modal || !modalContent) {
            console.error('❌ Modal não encontrado');
            return;
        }
        
        // Mostrar modal
        modal.style.display = 'block';
        modalContent.innerHTML = '<div class="loading">⏳ Carregando detalhes...</div>';
        
        const detailsUrl = API_URL + '&action_param=host_details&hostid=' + encodeURIComponent(hostid);
        
        fetch(detailsUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('👁️ Detalhes do host recebidos:', data);
            
            if (data.success && data.host) {
                displayHostDetails(data, modalContent);
            } else {
                modalContent.innerHTML = '<div class="error">❌ ' + (data.error || 'Erro ao carregar detalhes') + '</div>';
            }
        })
        .catch(error => {
            console.error('❌ Erro ao carregar detalhes:', error);
            modalContent.innerHTML = '<div class="error">❌ Erro de conexão</div>';
        });
    }
    
    // 🔧 Função para exibir detalhes do host no modal
    function displayHostDetails(data, container) {
        const host = data.host;
        const problems = data.problems || [];
        const availability = data.availability || 'unknown';
        const icmpping_value = data.icmpping_value;
        
        // 📊 Determinar status de disponibilidade via icmpping
        // O item icmpping retorna 1 se o host responde ao ping, 0 se não responde
        let availabilityText = '❓ Desconhecido';
        let availabilityClass = 'unknown';
        
        switch(availability) {
            case 'available':
                availabilityText = '🟢 Disponível (icmpping: 1)';
                availabilityClass = 'available';
                break;
            case 'unavailable':
                availabilityText = '🔴 Indisponível (icmpping: 0)';
                availabilityClass = 'unavailable';
                break;
            default:
                availabilityText = '❓ Sem monitoramento icmpping';
                availabilityClass = 'unknown';
        }
        
        let html = `
            <div class="host-detail-section">
                <h5>📋 Informações Básicas</h5>
                <p><strong>Nome:</strong> ${host.name}</p>
                <p><strong>Status:</strong> ${host.status == 0 ? '✅ Ativo' : '❌ Inativo'}</p>
                <p><strong>Disponibilidade:</strong> <span class="availability-${availabilityClass}">${availabilityText}</span></p>
            </div>
        `;
        
        if (host.interfaces && host.interfaces.length > 0) {
            html += `
                <div class="host-detail-section">
                    <h5>🌐 Interfaces</h5>
            `;
            
            host.interfaces.forEach(interface => {
                const typeNames = {1: 'Agent', 2: 'SNMP', 3: 'IPMI', 4: 'JMX'};
                html += `<p>${typeNames[interface.type] || 'Desconhecido'}: ${interface.ip || interface.dns}:${interface.port}</p>`;
            });
            
            html += '</div>';
        }
        
        if (problems.length > 0) {
            html += `
                <div class="host-detail-section">
                    <h5>🚨 Problemas (${problems.length})</h5>
            `;
            
            problems.forEach(problem => {
                html += `<p class="problem-item">• ${problem.name} <span class="severity-badge">S${problem.severity}</span></p>`;
            });
            
            html += '</div>';
        } else {
            html += '<div class="host-detail-section"><h5>🎉 Nenhum problema encontrado!</h5></div>';
        }
        
        // Adicionar seção de monitoramento se icmpping estiver disponível
        if (availability !== 'unknown') {
            html += `
                <div class="host-detail-section">
                    <h5>📊 Monitoramento</h5>
                    <p><strong>ICMP Ping:</strong> ${icmpping_value !== null ? icmpping_value : 'N/A'}</p>
                    <p><em>Disponibilidade verificada através do item key "icmpping"</em></p>
                </div>
            `;
        }
        
        container.innerHTML = html;
    }
    
    // 🚨 Função para mostrar erros
    function showError(message) {
        console.error('❌', message);
        // Você pode implementar um sistema de notificações aqui
    }
    
    // 🔥 Função para verificar se deve mostrar GIF especial
    function checkForSpecialGif(stats) {
        const gifContainer = document.getElementById('dynamic-gif-container');
        if (!gifContainer) return;
        
        const problems = parseInt(stats.problems) || 0;
        const hosts = parseInt(stats.hosts) || 0;
        const items = parseInt(stats.items) || 0;
        const triggers = parseInt(stats.triggers) || 0;
        
        let shouldShowGif = false;
        let gifData = null;
        
        // 🔥 Muitos problemas (>= 3) - valor baixo para teste
        if (problems >= 3) {
            shouldShowGif = true;
            gifData = {
                url: 'https://media.giphy.com/media/QMHoU66sBXqqLqYvGO/giphy.gif',
                text: '🔥 SISTEMA EM CHAMAS! Muitos problemas detectados!',
                type: 'fire'
            };
        }
        // 🚀 Muitos hosts (>= 10) - valor baixo para teste
        else if (hosts >= 10) {
            shouldShowGif = true;
            gifData = {
                url: 'https://media.giphy.com/media/3oriNYQX2lC6dfW2Ji/giphy.gif',
                text: '🚀 INFRAESTRUTURA MASSIVA! Muitos hosts monitorados!',
                type: 'rocket'
            };
        }
        // 📊 Muitos items (>= 100) - valor baixo para teste
        else if (items >= 100) {
            shouldShowGif = true;
            gifData = {
                url: 'https://media.giphy.com/media/3o7btQsLqXMJAPu6Na/giphy.gif',
                text: '📊 BIG DATA! Coletando muitas métricas!',
                type: 'data'
            };
        }
        // ⚡ Sistema carregado (triggers >= 20) - valor baixo para teste
        else if (triggers >= 20) {
            shouldShowGif = true;
            gifData = {
                url: 'https://media.giphy.com/media/26BRBKqUiq586bRVm/giphy.gif',
                text: '⚡ SISTEMA SUPER CARREGADO! Muitos triggers ativos!',
                type: 'lightning'
            };
        }
        // 🎉 Sistema tranquilo (poucos problemas)
        else if (problems === 0 && hosts > 0) {
            shouldShowGif = true;
            gifData = {
                url: 'https://media.giphy.com/media/3oriNZoNvn73MZaFYk/giphy.gif',
                text: '🎉 TUDO TRANQUILO! Sistema funcionando perfeitamente!',
                type: 'celebrate'
            };
        }
        
        if (shouldShowGif && gifData) {
            showDynamicGif(gifData, gifContainer);
        } else {
            hideDynamicGif(gifContainer);
        }
    }
    
    // 🎨 Função para mostrar GIF dinâmico
    function showDynamicGif(gifData, container) {
        const existingGif = container.querySelector('.dynamic-gif');
        
        // Se já existe um GIF do mesmo tipo, não fazer nada
        if (existingGif && existingGif.dataset.type === gifData.type) {
            return;
        }
        
        // Limpar container
        container.innerHTML = '';
        
        // Criar elemento do GIF
        const gifElement = document.createElement('div');
        gifElement.className = 'dynamic-gif';
        gifElement.dataset.type = gifData.type;
        
        gifElement.innerHTML = `
            <img src="${gifData.url}" alt="Dynamic GIF" class="dynamic-gif-image" />
            <div class="dynamic-gif-text">${gifData.text}</div>
        `;
        
        container.appendChild(gifElement);
        
        // Animação de entrada
        setTimeout(() => {
            gifElement.classList.add('show');
        }, 100);
        
        // Auto-hide após 10 segundos (opcional)
        setTimeout(() => {
            hideDynamicGif(container);
        }, 10000);
    }
    
    // 🙈 Função para esconder GIF dinâmico
    function hideDynamicGif(container) {
        const existingGif = container.querySelector('.dynamic-gif');
        if (existingGif) {
            existingGif.classList.add('hide');
            setTimeout(() => {
                container.innerHTML = '';
            }, 500);
        }
    }
    
    // 🎭 Função para animar GIF principal do cabeçalho
    function animateMainGif() {
        const mainGif = document.getElementById('main-gif');
        if (mainGif) {
            mainGif.style.transform = 'scale(1.1) rotate(5deg)';
            setTimeout(() => {
                mainGif.style.transform = 'scale(1) rotate(0deg)';
            }, 300);
        }
    }
    
    // 🎯 Event Listeners
    // 🎯 Event Listeners
    
    // Botão de carregar estatísticas
    // Load statistics button
    const loadStatsBtn = document.getElementById('load-stats');
    if (loadStatsBtn) {
        loadStatsBtn.addEventListener('click', loadStats);
        
        // Carregar estatísticas automaticamente ao carregar a página
        // Load statistics automatically when page loads
        setTimeout(loadStats, 500);
    }
    
    // Busca de hosts - CORRIGIDO
    // Host search - FIXED
    const searchInput = document.getElementById('host-search');
    const searchBtn = document.getElementById('search-btn');
    
    if (searchInput && searchBtn) {
        // Buscar ao clicar no botão
        // Search when clicking button
        searchBtn.addEventListener('click', searchHosts);
        
        // Buscar ao pressionar Enter - CORRIGIDO
        // Search when pressing Enter - FIXED
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchHosts();
            }
        });
        
        // Buscar enquanto digita (com delay)
        // Search while typing (with delay)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2) {
                    searchHosts();
                } else if (this.value.length === 0) {
                    const searchResults = document.getElementById('search-results');
                    if (searchResults) {
                        searchResults.innerHTML = '';
                    }
                }
            }, 500);
        });
    }
    
    // Botões de detalhes dos hosts
    // Host details buttons
    document.querySelectorAll('.host-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const hostid = this.getAttribute('data-hostid');
            showHostDetails(hostid);
        });
    });
    
    // Modal - fechar
    // Modal - close
    const modal = document.getElementById('host-modal');
    const closeBtn = document.querySelector('.close');
    
    if (modal && closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
    
    console.log('✅ JavaScript do Example Monzphere inicializado com sucesso! / Example Monzphere JavaScript initialized successfully!');
});

// 🎨 Efeitos visuais
const funImg = document.querySelector('.fun-section img');
if (funImg) {
    funImg.addEventListener('mouseenter', () => {
        funImg.style.transform = 'rotate(360deg)';
        funImg.style.transition = 'transform 0.6s ease';
    });
    
    funImg.addEventListener('mouseleave', () => {
        funImg.style.transform = 'rotate(0deg)';
    });
}
