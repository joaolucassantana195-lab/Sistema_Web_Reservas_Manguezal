const bodyClientes = document.querySelector('.body-clientes');
const errorDiv = document.querySelector('.nenhum-cliente');

function limparTexto(texto) {
    if (texto === null || texto === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(texto);
    return div.innerHTML;
}

// Função para garantir que o container exista na página
function obterContainerNotificacoes() {
    let container = document.querySelector('.container-notificacoes');
    if (!container) {
        container = document.createElement('div');
        container.classList.add('container-notificacoes');
        document.body.appendChild(container);
    }
    return container;
}

function exibirModalSucesso(mensagem) {
    const container = obterContainerNotificacoes();
    const div = document.createElement('div');
    div.classList.add('modal-confirm');
    div.innerText = mensagem;
    
    container.appendChild(div);

    setTimeout(() => {
        div.classList.add('saindo');
        setTimeout(() => {
            if (div.parentNode) div.parentNode.removeChild(div);
        }, 400);
    }, 2000);
}

function exibirModalErro(mensagem) {
    const container = obterContainerNotificacoes();
    const div = document.createElement('div');
    div.classList.add('modal-erro');
    div.innerText = mensagem || 'Ocorreu um erro inesperado!';
    
    container.appendChild(div);

    setTimeout(() => {
        div.classList.add('saindo');
        setTimeout(() => {
            if (div.parentNode) div.parentNode.removeChild(div);
        }, 400);
    }, 2500);
}

function abrirMenu(e) {
    const containerMenu = document.querySelector('.menu');
    const image = e.currentTarget.querySelector('img');

    if (containerMenu && image) {
        const estaAtivo = containerMenu.classList.toggle('ativo');

        image.src = estaAtivo 
            ? 'images/icone-closemenu.png' 
            : 'images/icone-menu.png';
    }
}

function buscarClientes(nome, email, telefone) {
    fetch('../admin/api/api_clientes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'nome': nome,
            'email': email,
            'telefone': telefone
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Erro na resposta da requisição');
        return response.json();
    })
    .then(clientes => {
        if (!bodyClientes) return;
        bodyClientes.innerHTML = '';
        
        if (!clientes || clientes.length === 0) {
            if (errorDiv) errorDiv.style.display = 'flex';
            return;
        }

        if (errorDiv) errorDiv.style.display = 'none';
        let htmlContent = '';

        // 1. Monta todo o HTML de uma vez
        clientes.forEach(element => {
            htmlContent += `
                <div class="linha-cliente">
                    <div class="celula-cliente"><div><strong>Nome</strong></div><div>${limparTexto(element.nome)}</div></div>
                    <div class="celula-cliente"><div><strong>Email</strong></div><div>${limparTexto(element.email)}</div></div>
                    <div class="celula-cliente"><div><strong>Telefone</strong></div><div>${limparTexto(element.telefone)}</div></div>
                    <div class="celula-cliente">
                        <div><strong>Total de visitas</strong></div>
                        <div id="visitas-cliente-${element.id}">${element.total_visitas || 'Carregando...'}</div>
                    </div>
                    <div class="celula-cliente">
                        <button onclick="verReservas(${element.id}, '${limparTexto(element.nome)}')" class="btn-verreservas">Ver reservas</button>
                    </div>
                </div>
            `;
        });

        bodyClientes.innerHTML = htmlContent;

        // 2. Busca o total de visitas para cada cliente após renderizar o HTML
        clientes.forEach(element => {
            carregarVisitasDoCliente(element.id);
        });
    })
    .catch(erro => {
        console.error('Erro ao buscar clientes');
        exibirModalErro('Erro de conexão ao carregar clientes');
    });
}

function carregarVisitasDoCliente(clienteId) {
    fetch(`../admin/api/api_clientesvisitas.php?id=${clienteId}`)
        .then(response => response.json())
        .then(visitas => {
            const containerVisitas = document.getElementById(`visitas-cliente-${clienteId}`);
            if (containerVisitas) {
                containerVisitas.innerText = visitas.total_linhas ?? 0;
            }
        })
        .catch(erro => {
            console.error(`Erro ao buscar visitas do cliente ${clienteId}:`);
        });
}

function verReservas(clienteid, nome) {
    const modal = document.createElement('div');
    modal.style = `
        position: fixed;
        top: 0;
        left: 0;
        min-width: 100%;
        min-height: 100%;
        background-color: rgba(0, 0, 0, 0.3);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `;
    modal.innerHTML = `
        <div class="varela-round-regular container-modal">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h5 style="text-align:center; margin:0; border:none;">Reservas Registradas</h5>
            <div class="container-btn-fechar">
                <span class="btn-fechar" style="cursor:pointer;">X</span>
            </div>
        </div>
            <p>${limparTexto(nome.toUpperCase())}</p>
            <div class="total-reservas">Total: 0</div>
            <div class="container-display-cliente">
                <div class="container-reservas-cliente"></div>
                <button class="btn-relatorio" onclick="downloadRelatorio(${clienteid}, '${limparTexto(nome)}')">Arquivo para impressão</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    modal.querySelector('.btn-fechar').addEventListener('click', () => {
        if (document.body.contains(modal)) {
            document.body.removeChild(modal);
        }
    });

    fetch(`../admin/api/api_clientesreservas.php?id=${clienteid}`)
        .then(response => response.json())
        .then(data => {
            const containerTotal = modal.querySelector('.total-reservas');
            const containerReservasCliente = modal.querySelector('.container-reservas-cliente');
            if (!containerReservasCliente) return;

            containerReservasCliente.innerHTML = '';

            // Atualiza o total direto do objeto recebido
            const listaReservas = data.reservas || [];
            const total = data.total_reservas ?? listaReservas.length;
            containerTotal.innerText = `Total: ${total}`;

            if (listaReservas.length === 0) {
                containerReservasCliente.innerHTML = '<p style="padding:10px;">Nenhuma reserva encontrada.</p>';
                return;
            }

            // Percorre a lista de reservas
            listaReservas.forEach(element => {
                let partesData = element.data ? element.data.substring(0, 10).split('-') : ['', '', ''];
                let dataFormatada = partesData[0] ? `${partesData[2]}/${partesData[1]}/${partesData[0].substring(2)}` : '-';
                let horarioFormatado = element.horario ? element.horario.slice(0, -3) : '-';

                containerReservasCliente.innerHTML += `
                    <div class='reserva-cliente'>
                        <div class="celula-reserva"><div><strong>Horário</strong></div><div>${limparTexto(horarioFormatado)}</div></div>
                        <div class="celula-reserva data-reserva"><div><strong>Data</strong></div><div>${limparTexto(dataFormatada)}</div></div>
                        <div class="celula-reserva"><div><strong>Pessoas</strong></div><div class="celula-qtdpessoas">${limparTexto(element.quantidade_pessoas)}</div></div>
                        <div class="celula-reserva td-numero-mesa"><div><strong>Mesa</strong></div><div>${limparTexto(element.mesa)}</div></div>
                        <div class="celula-reserva td-status"><div><strong>Status</strong></div><div>${limparTexto(element.status)}</div></div>
                    </div>
                `;
            });
        })
        .catch(erro => {
            console.error(`Erro ao buscar reservas do cliente`);
            exibirModalErro('Erro de conexão ao carregar reservas');
        });
}
function filtrarClientes() {
    const iptNome = document.querySelector('#nome') ? document.querySelector('#nome').value : '';
    const iptEmail = document.querySelector('#email') ? document.querySelector('#email').value : '';
    const iptTelefone = document.querySelector('#telefone') ? document.querySelector('#telefone').value : '';

    buscarClientes(iptNome, iptEmail, iptTelefone);
}

function limparFiltros() {
    if (document.querySelector('#nome')) document.querySelector('#nome').value = '';
    if (document.querySelector('#email')) document.querySelector('#email').value = '';
    if (document.querySelector('#telefone')) document.querySelector('#telefone').value = '';

    buscarClientes();
}

function downloadRelatorio(id, nome) {
    let data = new Date();
    let dataformat = data.toLocaleString('pt-BR');

   let html = `
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #00608a; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        div { font-weight: bold; }
        </style>
        </head>
        <body>
        <div>
            <p>Restaurante Manguezal</p>
            <p>${limparTexto(dataformat)}</p>
            <p>Cliente: ${limparTexto(nome)}</p>
        </div>
        <h2>Histórico de Reservas</h2>
        <table>
        <thead>
        <tr>
        <th>Data</th>
        <th>Horário</th>
        <th>Pessoas</th>
        <th>Mesa</th>
        <th>Status</th>
        </tr>
        </thead>
        <tbody>
    `;

    fetch(`../admin/api/api_clientesreservas.php?id=${id}`)
        .then(response => response.json())
        .then(data => {

            let dados = data.reservas || []

            dados.forEach(element => {
                let partesData = element.data.substring(0, 10).split('-');
                let dataFormatada = `${partesData[2]}/${partesData[1]}/${partesData[0].substring(2)}`;
                let horarioFormatado = element.horario.slice(0, -3);

                html += `
                    <tr>
                    <td>${limparTexto(dataFormatada)}</td>
                    <td>${limparTexto(horarioFormatado)}</td>
                    <td>${limparTexto(element.quantidade_pessoas)}</td>
                    <td>${limparTexto(element.mesa)}</td>
                    <td>${limparTexto(element.status)}</td>
                    </tr>
                `;
            });

            html += `
                </tbody>
                </table>
                </body>
                </html>
            `;

            const blob = new Blob([html], { type: 'text/html;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `relatorio_reservas_${id}.html`;
            a.click();
            URL.revokeObjectURL(url);
            exibirModalSucesso('Relatório gerado com sucesso!');
        })
        .catch(erro => {
            console.error(`Erro ao buscar reservas do cliente`);
            exibirModalErro('Erro de conexão ao gerar relatório');
        });
}

function sair() {
    const modal = document.createElement('div');
    modal.style = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.30);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        font-size: 20px;
    `;
    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036;">
            <p>Deseja fazer Logout do sistema?</p>
            <div class="divconcluir-acoes" style="width:100%; display:flex; justify-content:end; gap:10px;">
                <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    modal.querySelector('.btn-confirmar').addEventListener('click', () => {
        document.body.removeChild(modal);
        window.location.href = '../admin/logout.php';
    });

    modal.querySelector('.btn-cancelar').addEventListener('click', () => {
        document.body.removeChild(modal);
    });
}

buscarClientes();

window.addEventListener('pageshow', function (event) {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
      window.location.reload();
    }
  });

  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible') {
      window.location.reload();
    }
  });