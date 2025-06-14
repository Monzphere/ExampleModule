# 🎯 Example Monzphere - Tutorial Interativo / Interactive Tutorial

![image](https://github.com/user-attachments/assets/0299be87-d1ac-4de1-a7ab-cc7e54473b25)


## 🌍 Multilingual Module / Módulo Multilíngue

Este módulo foi desenvolvido com suporte completo à internacionalização do Zabbix, incluindo comentários bilíngues (Português/Inglês) e uso da função `_()` para tradução.

This module was developed with complete support for Zabbix internationalization, including bilingual comments (Portuguese/English) and use of the `_()` function for translation.

## 🔧 Funcionalidades Implementadas / Implemented Features

### 🌐 Sistema de Tradução / Translation System
- **Função `_()`**: Todas as strings visíveis ao usuário usam `_()` para tradução automática
- **Function `_()`**: All user-visible strings use `_()` for automatic translation
- **Comentários Bilíngues**: Todo código possui comentários em português e inglês
- **Bilingual Comments**: All code has comments in Portuguese and English

### 🎬 GIFs Dinâmicos / Dynamic GIFs
- **🔥 Fire GIF**: Quando problemas >= 3 / When problems >= 3
- **🚀 Rocket GIF**: Quando hosts >= 10 / When hosts >= 10  
- **📊 Data GIF**: Quando items >= 100 / When items >= 100
- **⚡ Lightning GIF**: Quando triggers >= 20 / When triggers >= 20
- **🎉 Celebration GIF**: Quando sem problemas / When no problems

### 🔍 Busca Nativa / Native Search
- **JSON-RPC**: Usa método nativo `search` do Zabbix
- **JSON-RPC**: Uses Zabbix native `search` method
- **Busca de Hosts**: Identifica e exibe apenas hosts
- **Host Search**: Identifies and displays only hosts

### 📊 Monitoramento / Monitoring  
- **ICMP Ping**: Disponibilidade via item key `icmpping`
- **ICMP Ping**: Availability via `icmpping` item key
- **Estatísticas**: Hosts, problemas, items, triggers
- **Statistics**: Hosts, problems, items, triggers

## 📁 Estrutura de Arquivos / File Structure

```
example-module/
├── Module.php                    # Classe principal / Main class
├── manifest.json                 # Configuração do módulo / Module configuration
├── actions/
│   ├── ExampleMonzphereView.php  # Controller da view / View controller
│   └── ExampleMonzphereData.php  # Controller de dados / Data controller
├── views/
│   └── examplemonzphere.view.php # Template da interface / Interface template
└── assets/
    ├── css/
    │   └── examplemonzphere.css  # Estilos / Styles
    └── js/
        └── examplemonzphere.js   # JavaScript interativo / Interactive JavaScript
```

## 🚀 Como Usar / How to Use

### 1. Instalação / Installation
```bash
# Copiar para o diretório de módulos do Zabbix
# Copy to Zabbix modules directory
cp -r example-module /usr/share/zabbix/modules/

# Acessar Administration > General > Modules no Zabbix
# Access Administration > General > Modules in Zabbix
```

### 2. Tradução / Translation
```php
// Exemplo de uso da função _() / Example of _() function usage
$title = _('🎯 Example Monzphere');
$message = _('Bem-vindo ao tutorial!');

// O Zabbix automaticamente traduzirá baseado no locale do usuário
// Zabbix will automatically translate based on user locale
```

### 3. Personalização de GIFs / GIF Customization
```javascript
// Modificar valores para ativar GIFs / Modify values to activate GIFs
if (problems >= 3) {  // Alterar aqui / Change here
    showGif({
        url: "custom-fire.gif",
        text: "🔥 CUSTOM MESSAGE!",
        type: "fire"
    });
}
```

## 📚 Conceitos Demonstrados / Demonstrated Concepts

### 🏗️ Arquitetura do Módulo / Module Architecture
- **Namespace**: Organização adequada do código
- **Namespace**: Proper code organization
- **Controllers**: Separação entre View e Data
- **Controllers**: Separation between View and Data
- **Assets**: CSS e JS organizados
- **Assets**: Organized CSS and JS

### 🔗 API do Zabbix / Zabbix API
```php
// Exemplos de uso / Usage examples
$hosts = API::Host()->get([...]);
$problems = API::Problem()->get([...]);
$items = API::Item()->get([...]);
```

### 📡 AJAX e JSON-RPC / AJAX and JSON-RPC
```javascript
// Busca de hosts / Host search
const payload = {
    jsonrpc: "2.0",
    method: "search",
    params: { search: "hostname" },
    id: 1
};

fetch('/jsrpc.php?output=json-rpc', {
    method: 'POST',
    body: JSON.stringify(payload)
});
```

## 🎨 Temas e Responsividade / Themes and Responsiveness

O módulo é compatível com todos os temas do Zabbix:
The module is compatible with all Zabbix themes:

- **🌙 Dark Theme**: Cores adaptadas automaticamente
- **🌙 Dark Theme**: Colors automatically adapted  
- **🌞 Blue Theme**: Suporte completo
- **🌞 Blue Theme**: Full support
- **📱 Mobile**: Layout responsivo
- **📱 Mobile**: Responsive layout

## 🔧 Configuração Avançada / Advanced Configuration

### Personalizar Limiares de GIF / Customize GIF Thresholds
```javascript
// Em assets/js/examplemonzphere.js
// In assets/js/examplemonzphere.js
const THRESHOLDS = {
    FIRE_PROBLEMS: 3,      // Problemas para GIF de fogo
    ROCKET_HOSTS: 10,      // Hosts para GIF de foguete  
    DATA_ITEMS: 100,       // Items para GIF de dados
    LIGHTNING_TRIGGERS: 20 // Triggers para GIF de raio
};
```

### Adicionar Novas Traduções / Add New Translations
```php
// Adicionar nova string traduzível / Add new translatable string
$new_text = _('Nova funcionalidade implementada!');

// O sistema de tradução do Zabbix irá procurar por esta string
// Zabbix translation system will look for this string
```

## 📖 Documentação Adicional / Additional Documentation

- **🔍 Busca**: Como implementar busca nativa de hosts / How to implement native host search
- **🎬 Animações**: Sistema de GIFs dinâmicos / Dynamic GIFs system  
- **📊 Monitoramento**: Uso de icmpping para disponibilidade / Using icmpping for availability
- **🌐 I18n**: Sistema de internacionalização / Internationalization system

## 🤝 Contribuição / Contributing

Este é um projeto educativo! Contribuições são bem-vindas:
This is an educational project! Contributions are welcome:

1. **Fork** o repositório / Fork the repository
2. **Crie** uma branch para sua feature / Create a branch for your feature  
3. **Implemente** melhorias / Implement improvements
4. **Adicione** comentários bilíngues / Add bilingual comments
5. **Use** a função `_()` para novas strings / Use `_()` function for new strings
6. **Envie** um Pull Request / Submit a Pull Request

## 📄 Licença / License

Este projeto é distribuído sob licença MIT para fins educativos.
This project is distributed under MIT license for educational purposes.

---

**🎯 Example Monzphere** - Tutorial prático de módulos Zabbix com suporte multilíngue e funcionalidades interativas / Practical Zabbix module tutorial with multilingual support and interactive features.

**Desenvolvido por / Developed by**: Monzphere Team 👨‍💻 (monzphere.com)
