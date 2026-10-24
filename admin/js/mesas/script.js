const body = document.querySelector('body');
const displayMesas = document.querySelector('.display-mesas');
const inputNumero = document.querySelector('#numeroMesa');
const inputCapacidade = document.querySelector('#capacidadeMesa');
const selectlocalizacao = document.querySelector('#localizacaoMesa');
const spanerro = document.querySelector('#span-erro');
const errorDiv = document.querySelector('.sem-mesas');

function limparTexto(texto) {
    if (texto === null || texto === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(texto);
    return div.innerHTML;
}

// Função para garantir que o container de notificações exista
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

function atualizarStatus(status, cliente, reservaId) {
    if (!cliente) {
        return 'Nenhum';
    }
    return cliente;
}
function atualizarStatusOcupacao(tipo_ocupacao) {
    if (!tipo_ocupacao) {
        return 'Desocupada';
    }
    return String(tipo_ocupacao).toUpperCase();
}

function atualizarMesas(numero, status, local) {
    fetch(`../admin/api/api_mesas.php`, {
        method: 'POST',
        headers:{
            'Content-Type' : 'application/json'
        },
        body: JSON.stringify({
            numero: numero,
            status: status,
            local: local
        })
    })
        .then(response => response.json())
        .then(mesas => {
            displayMesas.innerHTML = '';

            mesas.forEach(element => {
                const acaoTexto = element.ocupacao === 'Ocupada' ? 'Liberar' : 'Ocupar';
                const podeRemover = typeof perfilUsuario !== 'undefined' && perfilUsuario !== 'atendente';
                
                const reservaId = element.reserva_id || element.reserva_pendente_id || null;
                const estaEmAtendimento = !!element.reserva_id;
                
                

                displayMesas.innerHTML += `
                    <div class="card-mesa card-${limparTexto(element.ocupacao.toLowerCase())}">
                        <div class="card-fechar">
                        <span><span class="mesa-${limparTexto(element.mesa_local.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, ''))}">${limparTexto(element.mesa_local.toUpperCase())}</span></span>
                            ${podeRemover ? `<span class="btn-remover" onclick="removerMesa(event, ${element.mesa_id})"><img src="images/icone-lixeira.png" alt="Remover" width="100%" height="auto"></span>` : ''}
                        </div>
                        <div class="content-card">
                            <span class="card-numero">${limparTexto(element.mesa_numero)}</span>
                            <span><strong>Capacidade máx.: </strong> ${limparTexto(element.mesa_capacidade)}</span>
                            <span><strong>Reservante: </strong><span>${limparTexto(atualizarStatus(element.ocupacao.toLowerCase(), element.cliente_nome, reservaId))}</span></span>
                            <span><strong>Ocupação: </strong><span>${limparTexto(atualizarStatusOcupacao(element.mesa_tipo_ocupacao))}</span></span>
                            <div class="card-acoes">
                                <button class="btn-${limparTexto(element.ocupacao.toLowerCase())}" onclick="ocuparMesa(${element.mesa_id}, '${element.ocupacao}', ${element.mesa_numero}, ${reservaId}, ${estaEmAtendimento}, '${element.mesa_tipo_ocupacao}')">
                                    ${limparTexto(acaoTexto)}
                                </button>
                            </div>
                        </div>
                    </div>`;
            });

            if (errorDiv) {
                errorDiv.style.display = mesas.length !== 0 ? 'none' : 'flex';
            }
        })
        .catch(error => console.error('Erro ao buscar mesas'));
}

atualizarMesas()

function adicionarMesa(n, cap, loc) {
    if(!n){
        if (!inputNumero.value || !inputCapacidade.value || !selectlocalizacao.value) {
        spanerro.style.display = 'block';
        spanerro.style.color = 'red';
        setTimeout(() => {
            spanerro.style.display = 'none';
        }, 1000);
        return;
    }
    }
   

    const numero = n|| inputNumero.value;
    const capacidade = cap || inputCapacidade.value;
    const localizacao = loc || selectlocalizacao.value;

    fetch('../admin/api/api_adicionarmesas.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            numero: numero,
            capacidade: capacidade,
            localizacao: localizacao
        })
    })
    .then(async response => {
        const texto = await response.text();
        if (!texto) throw new Error("Resposta vazia do servidor.");
        return JSON.parse(texto);
    })
    .then(data => {
        if (data.sucesso) {
            if(!n){
                atualizarMesas(); 
            }
            
        } else {
            exibirModalErro(data.erro || 'Erro desconhecido ao adicionar mesa.');
        }
    })
    .catch(error => {
        console.error('Erro na requisição');
    });
}

function removerMesa(e, id) {
    if (id) {
        fetch('../admin/api/api_removermesas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                numero: id
            })
        })
        .then(async response => {
            const texto = await response.text();
            if (!texto) throw new Error("Resposta vazia do servidor.");
            return JSON.parse(texto);
        })
        .then(data => {
            if (data.sucesso) {
                atualizarMesas(); 
            } else {
                exibirModalErro(data.erro || 'Erro desconhecido ao remover mesa.');
            }
        })
        .catch(error => {
            console.error('Erro na requisição');
        });
    }
}

function sair() {
    const modal = document.createElement('div');
    modal.style = `
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100vh;
        background-color: rgba(0, 0, 0, 0.3);
        display: flex; justify-content: center; align-items: center;
        z-index: 9999; font-size: 20px;
    `;

    modal.innerHTML = `
        <div class="varela-round-regular content-model" style="background-color:#ececec; color:#00608a; padding:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036;">
            <p>Deseja fazer Logout do sistema?</p>
            <div class="divconcluir-acoes" style="width:100%; display:flex; justify-content:end; gap:10px;">
                <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    body.appendChild(modal);

    modal.querySelector('.btn-confirmar').addEventListener('click', () => {
        body.removeChild(modal);
        window.location.href = '../admin/logout.php';
    });

    modal.querySelector('.btn-cancelar').addEventListener('click', () => {
        body.removeChild(modal);
    });
}

function alterarStatusMesa(id, novoStatus, tipo_ocupacao) {

    fetch('../admin/api/api_statusmesa.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: id,
            status: novoStatus,
            tipo_ocupacao: tipo_ocupacao
        })
    })
    .then(async response => {
        const texto = await response.text();
        if (!texto) throw new Error("Resposta vazia do servidor.");
        return JSON.parse(texto);
    })
    .then(data => {
        if (data.sucesso) {
            atualizarMesas();
        } else {
            exibirModalErro(data.erro || 'Erro ao alterar status');
        }
    })
    .catch(error => {
        console.error('Erro na requisição');
    });
}

function atualizarStatusReserva(e, id, status, mesa, acao, button) {
    if (e) e.preventDefault();

    if (button) {
        button.disabled = true;
        button.textContent = 'Processando...';
    }

    fetch('../admin/api/api_atualizarstatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, status, mesa })
    })
    .then(async response => {
        const texto = await response.text();
        if (!texto) throw new Error("Resposta vazia.");
        return JSON.parse(texto);
    })
    .then(data => {
        if (data.sucesso) {
            let mensagem = 'Status atualizado com sucesso!';
            if (acao === 'confirmar' || acao === 'cancelar') {
                mensagem = 'Email enviado ao cliente!';
            } else if (acao === 'concluir') {
                mensagem = 'Reserva concluída!';
            } else if (acao === 'nao_compareceu') {
                mensagem = 'Reserva marcada como não compareceu!';
            }

            exibirModalSucesso(mensagem);

            if (typeof atualizarReservas === 'function') atualizarReservas();
            atualizarMesas();

        } else {
            exibirModalErro(data.erro || 'Ocorreu um erro ao realizar a ação!');
        }
    })
    .catch(error => {
        console.error('Erro');
        exibirModalErro('Erro na conexão com o servidor.');
    })
    .finally(() => {
        if (button) {
            button.disabled = false;
            button.textContent = 'Confirmar';
        }
    });
}

function ocuparMesa(id, status, numeroMesa, reservaId = null, estaEmAtendimento = false, tipo_ocupacao) {
    const estaOcupada = status === 'Ocupada';

    // 1. FLUXO DE LIBERAR MESA
    if (estaOcupada) {
        if (reservaId && estaEmAtendimento) {
            if(tipo_ocupacao === 'reserva'){
            const modalLiberar = document.createElement('div');
            modalLiberar.style = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
                background-color: rgba(0, 0, 0, 0.4); display: flex;
                justify-content: center; align-items: center; z-index: 9999; font-size: 18px;
            `;

            modalLiberar.innerHTML = `
                <div class="varela-round-regular content-model" style="background-color:#ececec; color:#00608a; padding:25px; border-radius:15px; border:2px solid #00608a; min-width:320px;">
                    <p style="margin-bottom:15px; font-weight:bold;">Liberar mesa ${limparTexto(numeroMesa)}</p>
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:16px; margin-bottom:20px;">
                        <input type="checkbox" id="chkConcluirReserva" checked style="width:20px; height:20px; cursor:pointer;">
                        Deseja marcar a reserva como concluída?
                    </label>
                    <div style="width:100%; display:flex; justify-content:end; gap:10px;">
                        <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                        <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
                    </div>
                </div>
            `;

            body.appendChild(modalLiberar);

            modalLiberar.querySelector('.btn-confirmar').addEventListener('click', () => {
                const chkConcluir = modalLiberar.querySelector('#chkConcluirReserva').checked;

                if (chkConcluir) {
                    atualizarStatusReserva(null, reservaId, 'Concluída', id, 'concluir', null);
                    alterarStatusMesa(id, 'Livre');
                } else {
                    alterarStatusMesa(id, 'Livre');
                }

                if (body.contains(modalLiberar)) body.removeChild(modalLiberar);
            });

            modalLiberar.querySelector('.btn-cancelar').addEventListener('click', () => {
                if (body.contains(modalLiberar)) body.removeChild(modalLiberar);
            });
        }else{
            alterarStatusMesa(id, 'Livre');
        }

        } else {
            alterarStatusMesa(id, 'Livre');
        }
        return;
    }

    // 2. FLUXO DE OCUPAR MESA
    if (reservaId) {
        const modalOcupar = document.createElement('div');
        modalOcupar.style = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
            background-color: rgba(0, 0, 0, 0.4); display: flex;
            justify-content: center; align-items: center; z-index: 9999; font-size: 18px;
        `;

        modalOcupar.innerHTML = `
            <div class="varela-round-regular content-model" style="background-color:#ececec; color:#00608a; padding:25px; border-radius:15px; border:2px solid #00608a; min-width:320px;">
                <p style="margin-bottom:15px; font-weight:bold;">Ocupar mesa ${limparTexto(numeroMesa)}</p>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:16px; margin-bottom:20px;">
                    <input type="checkbox" id="chkClienteReserva" checked style="width:20px; height:20px; cursor:pointer;">
                    É o cliente da reserva?
                </label>
                <div style="width:100%; display:flex; justify-content:end; gap:10px;">
                    <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                    <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
                </div>
            </div>
        `;

        body.appendChild(modalOcupar);

        modalOcupar.querySelector('.btn-confirmar').addEventListener('click', () => {
            const ehReserva = modalOcupar.querySelector('#chkClienteReserva').checked;

            if (ehReserva) {
                atualizarStatusReserva(null, reservaId, 'Em Atendimento', id, 'atender', null);
                alterarStatusMesa(id, 'Ocupada', 'reserva');
            } else {
                alterarStatusMesa(id, 'Ocupada', 'passante');
            }

            if (body.contains(modalOcupar)) body.removeChild(modalOcupar);
        });

        modalOcupar.querySelector('.btn-cancelar').addEventListener('click', () => {
            if (body.contains(modalOcupar)) body.removeChild(modalOcupar);
        });

    } else {
        alterarStatusMesa(id, 'Ocupada', 'passante');
    }
}

function iterarMesas() {
    const modalIterar = document.createElement('div');
    modalIterar.style = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background-color: rgba(0, 0, 0, 0.4); display: flex;
        justify-content: center; align-items: center; z-index: 9999; font-size: 18px;
    `;

    modalIterar.innerHTML = `
        <div class="varela-round-regular content-model" style="background-color:#ececec; color:#00608a; padding:25px; border-radius:15px; border:2px solid #00608a; min-width:320px;">
            <p style="margin-top:0; font-weight:bold;">Iterar Mesas</p>
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;">
                <label style="display:flex; align-items:center; cursor:pointer; font-size:15px;">Início:</label>
                <input type="number" id="iteracao-inicio" style="width:100%; height:30px; padding:0 5px;" min="1">
                
                <label style="display:flex; align-items:center; cursor:pointer; font-size:15px;">Fim:</label>
                <input type="number" id="iteracao-fim" style="width:100%; height:30px; padding:0 5px;" min="1">
                
                <label style="display:flex; align-items:center; cursor:pointer; font-size:15px;">Capacidade máx.:</label>
                <input type="number" id="iteracao-capacidade" style="width:100%; height:30px; padding:0 5px;" min="1">
                
                <label style="display:flex; align-items:center; cursor:pointer; font-size:15px;">Localização:</label>
                <select id="iteracao-local" style="width:100%; height:34px;">
                    <option value="beach">Beach Club</option>
                    <option value="deck" selected>Deck</option>
                    <option value="salão">Salão Principal</option>
                </select>
                <span id="spn-err" style="color:red; font-size:14px; font-weight:400; min-height:18px;"></span>
            </div>
            
            <div style="width:100%; display:flex; justify-content:flex-end; gap:10px;">
                <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    document.body.appendChild(modalIterar);

    modalIterar.addEventListener('click', (e) => {
        if (e.target === modalIterar) {
            fecharModal();
        }
    });

    const fecharModal = () => {
        if (document.body.contains(modalIterar)) {
            document.body.removeChild(modalIterar);
        }
    };

    modalIterar.querySelector('.btn-confirmar').addEventListener('click', () => {
        const inicioVal = modalIterar.querySelector('#iteracao-inicio').value;
        const fimVal = modalIterar.querySelector('#iteracao-fim').value;
        const capacidadeVal = modalIterar.querySelector('#iteracao-capacidade').value;
        const selectLocal = modalIterar.querySelector('#iteracao-local').value;
        const spanErro = modalIterar.querySelector('#spn-err');

        // Conversão para inteiros
        const inicio = parseInt(inicioVal, 10);
        const fim = parseInt(fimVal, 10);
        const capacidade = parseInt(capacidadeVal, 10);

        // Validação de campos vazios ou inválidos
        if (isNaN(inicio) || isNaN(fim) || isNaN(capacidade) || !selectLocal) {
            spanErro.textContent = 'Preencha todos os campos corretamente!';
            return;
        }

        // Validação lógica do intervalo
        if (inicio > fim) {
            spanErro.textContent = 'O número inicial não pode ser maior que o final!';
            return;
        }

        spanErro.textContent = '';

        for (let i = inicio; i <= fim; i++) {
            adicionarMesa(i, capacidade, selectLocal);
        }

        if (typeof atualizarMesas === 'function') {
            atualizarMesas();
        }

        fecharModal();
    });

    modalIterar.querySelector('.btn-cancelar').addEventListener('click', fecharModal);
}

function filtrar(e) {

    if (e && typeof e.preventDefault === 'function') {
        e.preventDefault();
    }

    const elNumero = document.querySelector("#numero");
    const elStatus = document.querySelector("#status");
    const elLocal = document.querySelector("#local");

   
    const iptNumeroVal = elNumero ? elNumero.value.trim() : "";
    const iptStatusVal = elStatus ? elStatus.value.trim() : "";
    const iptLocalVal  = elLocal ? elLocal.value.trim() : "";

    
    const numero = iptNumeroVal !== "" ? Number(iptNumeroVal) : null;
    const status = iptStatusVal !== "" ? iptStatusVal : null;
    const local  = iptLocalVal !== "" ? iptLocalVal : null;

  
    if (typeof atualizarMesas === 'function') {
        atualizarMesas(numero, status, local);
    }
}

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