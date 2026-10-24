const body = document.querySelector('body');
const corpoTabela = document.querySelector('#body-reservas');
const errorDiv = document.querySelector('.sem-reservas');

// Função para garantir que o container exista na página

function limparTexto(texto) {
    if (texto === null || texto === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(texto);
    return div.innerHTML;
}

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


document.addEventListener('click', () => {
    document.querySelectorAll('.custom-select-container').forEach(select => select.classList.remove('open'));
});

window.addEventListener('scroll', () => {
    document.querySelectorAll('.custom-select-container.open').forEach(selectContainer => {
        posicionarMenuContainer(selectContainer);
    });
}, true);

window.addEventListener('resize', () => {
    document.querySelectorAll('.custom-select-container.open').forEach(selectContainer => {
        posicionarMenuContainer(selectContainer);
    });
});

document.addEventListener('click', (e) => {
    const trigger = e.target.closest('.select-trigger');
    const option = e.target.closest('.custom-option');
    const allSelects = document.querySelectorAll('.custom-select-container');

    if (trigger) {
        e.stopPropagation();
        const selectContainer = trigger.closest('.custom-select-container');

        allSelects.forEach(otherSelect => {
            if (otherSelect !== selectContainer) otherSelect.classList.remove('open');
        });

        selectContainer.classList.toggle('open');

        if (selectContainer.classList.contains('open')) {
            posicionarMenuContainer(selectContainer);
        }
        return;
    }

    if (option) {
        const selectContainer = option.closest('.custom-select-container');
        const selectedText = selectContainer.querySelector('.selected-text');

        if (selectedText) {
            selectedText.innerText = option.innerText;
        }
        selectContainer.classList.remove('open');
        return;
    }

    allSelects.forEach(select => select.classList.remove('open'));
});

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

function posicionarMenuContainer(selectContainer) {
    const trigger = selectContainer.querySelector('.select-trigger');
    const optionsList = selectContainer.querySelector('.select-options');
    if (trigger && optionsList) {
        const rect = trigger.getBoundingClientRect();
        optionsList.style.top = (rect.bottom + 4) + 'px';
        optionsList.style.left = rect.left + 'px';
        optionsList.style.width = rect.width + 'px';
    }
}

function ativarArrasteNosContaineres() {
    const containers = document.querySelectorAll('.tr-scroll');

    containers.forEach(slider => {
        let isDown = false, startX, scrollLeft;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener('mouseleave', () => isDown = false);
        slider.addEventListener('mouseup', () => isDown = false);
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            slider.scrollLeft = scrollLeft - (x - startX) * 2;
        });
    });
}

function gerarHtmlSelect(status, mesa, reserva_id) {
    if (mesa && mesa !== 'null' && mesa !== 'undefined' && 
        ['Confirmada', 'Concluída', 'Não compareceu', 'Em Atendimento'].includes(status)) {
            if(status ==='Confirmada' || status === 'Em Atendimento'){
                return `<div class="container-numero-mesa"><span class="numero-mesa">${mesa}</span><span onclick="alterarMesa(event, ${reserva_id})" class="btn-alterar-mesa"><img src="images/alterarMesa.png"></span></div>`;
            }else{
                return `<div class="container-numero-mesa"><span class="numero-mesa">${mesa}</span></div>`;
            }
        
    }
    if (status === 'Cancelada') {
        return `<div>S/N</div>`;
    }
    return `
        <div class="custom-select-container">
            <div class="select-trigger">
                <span class="selected-text">Selecione uma opção</span>
                <i class="arrow"></i>
            </div>
            <ul class="select-options"></ul>
        </div>`;
}

function atualizarSelectPorLinha() {
    const linhas = document.querySelectorAll('.linha-reservas');

    linhas.forEach(linha => {
        const selectOptions = linha.querySelector('.select-options');
        if (!selectOptions) return;
        const dataReserva = linha.querySelector('.data-reserva')?.getAttribute('data-valor');
        const beachclub = linha.querySelector('.data-beach')?.getAttribute('data-beach');
        const qtdPessoas = linha.querySelector('.celula-qtdpessoas')?.textContent;
        if (!dataReserva) return;

        fetch(`../admin/api/api_mesas_select.php?data=${dataReserva}`)
            .then(r => {
                if (!r.ok) throw new Error("Erro ao buscar mesas.");
                return r.json();
            })
            .then(mesas => {
                selectOptions.innerHTML = '';
                
                let disponiveis = [];
                if (beachclub === 'Sim') {
                    disponiveis = mesas.filter(m =>
                        m.ocupacao === 'Livre' &&
                        m.mesa_ativa == true &&
                        m.mesa_capacidade >= parseInt(qtdPessoas) &&
                        m.mesa_local == 'beach'
                    ); 
                } else {
                    disponiveis = mesas.filter(m =>
                        m.ocupacao === 'Livre' &&
                        m.mesa_ativa == true &&
                        m.mesa_capacidade >= parseInt(qtdPessoas) &&
                        m.mesa_local != 'beach'
                    ); 
                }

                if (disponiveis.length === 0) {
                    selectOptions.innerHTML = `<li class="custom-option" data-value="null">Nenhuma mesa disponivel.</li>`;
                } else {
                    disponiveis.forEach(m => {
                        selectOptions.innerHTML += `<li class="custom-option" data-value="${m.mesa_id}">Mesa ${m.mesa_numero} ${m.mesa_local.toUpperCase()} (max.${m.mesa_capacidade})</li>`;
                    });
                }
            })
            .catch(error => {
               
                exibirModalErro('Não foi possível carregar as mesas disponíveis.');
            });
    });
}

function renderizarReservas(reservas) {
    let htmlCompleto = '';

    reservas.forEach(element => {
        let acoes = '';
        let classeStatus = '';
        let partesData = element.reserva_dia.substring(0, 10).split('-');
        let dataFormatada = `${partesData[2]}/${partesData[1]}/${partesData[0].substring(2)}`;
        let horarioFormatado = element.reserva_horario.slice(0, -3);
        let telefoneFormatado = `(${element.cliente_telefone.slice(0, -9)}) ${element.cliente_telefone.slice(3, -7)} ${element.cliente_telefone.slice(3, -4)}-${element.cliente_telefone.slice(7)}`;
        let reservabeach = element.reserva_beachclub == 1 ? 'Sim' : 'Não';
        
        if (element.reserva_status === 'Aguardando') {
            classeStatus = 'status-aguardando';
            acoes = `<button class="btn-confirmar" onclick="confirmarReserva(event, ${element.reserva_id})">Confirmar</button>
                     <button class="btn-cancelar" onclick="cancelarReserva(event, ${element.reserva_id})">Cancelar</button>`;
        } else if (element.reserva_status === 'Confirmada') {
            classeStatus = 'status-confirmada';
            acoes = `<button class="btn-concluida" onclick="concluirReserva(event, ${element.reserva_id}, ${element.mesa_id})">Concluída</button>
                     <button class="btn-nao-compareceu" onclick="naocompareceuReserva(event, ${element.reserva_id})">Não Compareceu</button>`;
        } else if (element.reserva_status === 'Em Atendimento') {
            classeStatus = 'status-ematendimento';
            acoes = `<button class="btn-concluida" onclick="concluirReserva(event, ${element.reserva_id}, ${element.mesa_id})">Concluída</button>`;
        } else if (element.reserva_status === 'Cancelada') {
            classeStatus = 'status-cancelada';
            acoes = ``;
        } else if (element.reserva_status === 'Concluída') {
            classeStatus = 'status-concluida';
            acoes = ``;
        } else if (element.reserva_status === 'Não compareceu') {
            classeStatus = 'status-nao-compareceu';
            acoes = ``;
        }

        
            htmlCompleto += `<div class="linha-reservas-${element.reserva_status.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, '')} linha-reservas card-reserva">
                <div class="celula-reservas td-status">
                        <span class="${classeStatus} status-reserva">${limparTexto(element.reserva_status)}</span>
                    </div>
                <!-- Cabeçalho do Cartão -->
                <div class="card-header">
                    <div class="cliente-info">
                        <h3 class="cliente-nome">${limparTexto(element.cliente_nome)}</h3>
                        <span class="cliente-telefone">${telefoneFormatado}</span>
                    </div>
                </div>

                <!-- Detalhes da Reserva -->
                <div class="container-celula card-body">
                    <div class="celula-reservas data-reserva" data-valor="${element.reserva_dia.substring(0, 10)}">
                        <span class="label">Data & Hora</span>
                        <span class="valor">${dataFormatada} às ${horarioFormatado}</span>
                    </div>

                    <div class="celula-reservas td-numero-mesa">
                        <span class="label">Mesa</span>
                        <span class="valor">${gerarHtmlSelect(element.reserva_status, element.mesa_numero, element.reserva_id)}</span>
                    </div>

                    <div class="celula-reservas">
                        <span class="label">Pessoas</span>
                        <span class="valor celula-qtdpessoas">${element.reserva_qtd_pessoas}</span>
                    </div>

                    <div class="celula-reservas data-beach" data-beach="${reservabeach}">
                        <span class="label">Beach Club</span>
                        <span class="valor">${reservabeach}</span>
                    </div>
                </div>

                <!-- Rodapé de Ações -->
                <div class="card-footer">
                    <div class="celula-reservas acoes-celula">
                        <button class="btn-observacao" onclick="verObservacoes('${limparTexto(element.cliente_nome)}','${limparTexto(element.reserva_observacoes)}')">Observações</button>
                    <div class="container-acoes-linha">
                        ${acoes}
                    </div>
                    </div>
                </div>

            </div>`;
        
    });

    corpoTabela.innerHTML = htmlCompleto;
    atualizarSelectPorLinha();
    ativarArrasteNosContaineres();
    if (errorDiv) errorDiv.style.display = reservas.length === 0 ? 'flex' : 'none';
}

function buscarReservas(filtros = {}) {
    fetch('../admin/api/api_reservas.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(filtros),
        cache: 'no-store'
    })
    .then(response => {
        if (!response.ok) throw new Error("Erro de comunicação com o servidor.");
        return response.json();
    })
    .then(reservas => renderizarReservas(reservas))
    .catch(error => {
        console.error('Erro ao carregar reservas');
        if (errorDiv) errorDiv.style.display = 'flex';
        exibirModalErro('Erro ao carregar a lista de reservas.');
    });
}

function atualizarReservas() {
    const urlParams = new URLSearchParams(window.location.search);
    const urldata = urlParams.get('data');
    
    if (urldata) {
        buscarReservas({ data: urldata });
    } else {
        buscarReservas();  
    }
}

atualizarReservas();

function filtrar(event) {
    if (event) event.preventDefault();
    window.history.pushState({}, '', window.location.pathname);
    
    const filtrodata = document.querySelector('#data')?.value;
    const filtrostatus = document.querySelector('#status')?.value;
    const filtronome = document.querySelector('#cliente')?.value;

    buscarReservas({
        data: filtrodata || null,
        st: filtrostatus || null,
        nome: filtronome || null
    });
}

function atualizarStatus(e, id, status, mesa, acao, button, motivo) {
    if (e) e.preventDefault();

    if (button) {
        button.disabled = true;
        button.textContent = 'Aguarde...';
    }

    fetch('../admin/api/api_atualizarstatus.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id, status, mesa, motivo })
    })
    .then(async response => {
        const texto = await response.text();
        if (!texto) throw new Error("Resposta vazia do servidor.");
        return JSON.parse(texto);
    })
    .then(data => {
        if (data.sucesso) {
            let mensagem = 'Ação realizada com sucesso!';
            if (acao === 'confirmar' || acao === 'cancelar') {
                mensagem = 'E-mail enviado ao cliente!';
            } else if (acao === 'concluir') {
                mensagem = 'Reserva concluída!';
            } else if (acao === 'nao_compareceu') {
                mensagem = 'Reserva marcada como não compareceu!';
            }
            
            exibirModalSucesso(mensagem);
            atualizarReservas();
        } else {
            exibirModalErro(data.erro || 'Ocorreu um erro ao realizar a ação!');
            atualizarReservas();
        }
    })
    .catch(error => {
       
        exibirModalErro('Ocorreu uma falha na conexão com o servidor.');
    })
    .finally(() => {
        if (button) button.disabled = false;
    });
}

function verObservacoes(nome, observacao) {
    const modal = document.createElement('div');
    modal.style = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.30); display: flex;
        justify-content: center; align-items: center; z-index: 9999; font-size:20px;
    `;
    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; margin:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036; width:600px; min-width:250px;">
            <p><strong>Observacões</strong></p>
            <p>Cliente: ${nome}</p>
            <div class='observacao-text'>
                <p>${observacao || 'Nenhuma observação informada.'}</p>
            </div>
            <button class="btn-fechar">Fechar</button>
        </div>
    `;

    body.appendChild(modal);
    modal.querySelector('.btn-fechar').addEventListener('click', () => {
        body.removeChild(modal);
    }); 
}

function confirmarReserva(e, id) {
    e.preventDefault();

    const linha = e.target.closest('.linha-reservas');
    if (!linha) return;

    const selectContainer = linha.querySelector('.custom-select-container');
    const selectedText = selectContainer?.querySelector('.selected-text');

    if (!selectedText) {
        atualizarStatus(e, id, 'Confirmada');
        return;
    }

    const valorTexto = selectedText.innerText.trim();

    if (valorTexto === '' || valorTexto === 'Selecione uma opcao') {
        exibirModalErro('Selecione uma mesa válida!');
        return;
    }

    const opcaoSelecionada = Array.from(selectContainer.querySelectorAll('.custom-option'))
        .find(opt => opt.innerText === valorTexto);

    const valorMesa = opcaoSelecionada?.getAttribute('data-value');

    if (!valorMesa || valorMesa === 'null') {
        exibirModalErro('Mesa indisponível.');
        return;
    }

    atualizarStatus(e, id, 'Confirmada', valorMesa, 'confirmar', e.target);
}

function cancelarReserva(e, id) {
    const modal = document.createElement('div');
    modal.style = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background-color: rgba(0, 0, 0, 0.30); display: flex;
        justify-content: center; align-items: center; z-index: 9999; font-size:20px;
    `;
    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; margin:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036; display:flex; flex-direction:column; gap:10px;">
            <p>Deseja cancelar a reserva?</p>
            <span>Motivo do cancelamento: </span>
            <textarea placeholder='Insira o motivo do cancelamento' style="font-size:15px;" id='motivo-cancelamento'></textarea>
            <div class="divconcluir-acoes" style="width:100%; display:flex; justify-content:end; gap:10px;">
                <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    body.appendChild(modal);

    modal.querySelector('.btn-confirmar').addEventListener('click', () => {
        let motivo = modal.querySelector('#motivo-cancelamento').value;
        atualizarStatus(e, id, 'Cancelada', null, 'cancelar', e.target, motivo);
        body.removeChild(modal);
    });

    modal.querySelector('.btn-cancelar').addEventListener('click', () => {
        body.removeChild(modal);
    }); 
}

function concluirReserva(e, id, mesa_id) {
    e.preventDefault();
    const modal = document.createElement('div');
    modal.style = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background-color: rgba(0, 0, 0, 0.30); display: flex;
        justify-content: center; align-items: center; z-index: 9999; font-size:20px;
    `;
    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; margin:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036;">
            <p>Deseja marcar a reserva como "Concluída"?</p>
            <div class="divconcluir-acoes" style="width:100%; display:flex; justify-content:end; gap:10px;">
                <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    body.appendChild(modal);

    modal.querySelector('.btn-confirmar').addEventListener('click', () => {
        atualizarStatus(e, id, 'Concluída', mesa_id, 'concluir');
        body.removeChild(modal);
    });

    modal.querySelector('.btn-cancelar').addEventListener('click', () => {
        body.removeChild(modal);
    });
}

function naocompareceuReserva(e, id) {
    e.preventDefault();
    const modal = document.createElement('div');
    modal.style = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background-color: rgba(0, 0, 0, 0.30); display: flex;
        justify-content: center; align-items: center; z-index: 9999; font-size:20px;
    `;
    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; margin:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036;">
            <p>Deseja marcar a reserva como "Não compareceu"?</p>
            <div class="divnaocomparecu-acoes" style="width:100%; display:flex; justify-content:end; gap:10px;">
                <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    body.appendChild(modal);

    modal.querySelector('.btn-confirmar').addEventListener('click', () => {
        atualizarStatus(e, id, 'Não compareceu', null, 'nao_compareceu', e.target);
        body.removeChild(modal);
    });

    modal.querySelector('.btn-cancelar').addEventListener('click', () => {
        body.removeChild(modal);
    }); 
}

function alterarMesa(e, reserva_id) {
    e.preventDefault();

    // Localiza a linha da reserva para pegar a data e informações necessárias
    const linha = e.target.closest('.linha-reservas');
    const dataReserva = linha?.querySelector('.data-reserva')?.getAttribute('data-valor');
    const beachclub = linha?.querySelector('.data-beach')?.getAttribute('data-beach');
    const qtdPessoas = linha?.querySelector('.celula-qtdpessoas')?.textContent || 1;

    if (!dataReserva) {
        exibirModalErro('Não foi possível identificar a data da reserva.');
        return;
    }

    // Cria a estrutura do modal
    const modal = document.createElement('div');
    modal.style = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.30); display: flex;
        justify-content: center; align-items: center; z-index: 9999; font-size: 20px;
    `;
    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; margin:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036; width:600px; min-width:250px; display: flex; flex-direction: column; gap: 15px;">
            <p><strong>Alterar Mesa</strong></p>
            <div>
                <label style="font-size: 16px; display: block; margin-bottom: 5px;">Selecione a nova mesa livre:</label>
                <select id="select-nova-mesa" style="width: 100%; padding: 10px; font-size: 16px; border-radius: 8px; border: 1px solid #00608a;">
                    <option value="">Carregando mesas disponíveis...</option>
                </select>
                <span class="spn-erro"></span>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:8px 16px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    body.appendChild(modal);

    const selectMesa = modal.querySelector('#select-nova-mesa');
    const btnConfirmar = modal.querySelector('.btn-confirmar');
    const btnCancelar = modal.querySelector('.btn-cancelar');

    // Busca as mesas disponíveis na API para aquela data
    fetch(`../admin/api/api_mesas_select.php?data=${dataReserva}`)
        .then(r => {
            if (!r.ok) throw new Error("Erro ao buscar mesas.");
            return r.json();
        })
        .then(mesas => {
            selectMesa.innerHTML = '';
            
            let disponiveis = [];
            if (beachclub === 'Sim') {
                disponiveis = mesas.filter(m =>
                    m.ocupacao === 'Livre' &&
                    m.mesa_ativa == true &&
                    m.mesa_capacidade >= parseInt(qtdPessoas) &&
                    m.mesa_local == 'beach'
                ); 
            } else {
                disponiveis = mesas.filter(m =>
                    m.ocupacao === 'Livre' &&
                    m.mesa_ativa == true &&
                    m.mesa_capacidade >= parseInt(qtdPessoas) &&
                    m.mesa_local != 'beach'
                ); 
            }

           if (disponiveis.length === 0) {
                selectMesa.innerHTML = `<option value="">Nenhuma mesa disponível com capacidade suficiente.</option>`;
                btnConfirmar.disabled = true;
                btnConfirmar.style.opacity = '0.5';
            } else {
                selectMesa.innerHTML = `<option value="">Selecione...</option>`;
                disponiveis.forEach(m => {
                    selectMesa.innerHTML += `<option value="${m.mesa_id}">Mesa ${limparTexto(m.mesa_numero)} - ${limparTexto(m.mesa_local.toUpperCase())} (Máx. ${m.mesa_capacidade} pessoas)</option>`;
                });
            }
        })
        .catch(error => {
            
            exibirModalErro('Não foi possível carregar as mesas disponíveis.');
            body.removeChild(modal);
        });

    // Ação ao confirmar a troca
    btnConfirmar.addEventListener('click', () => {
        const novaMesaId = selectMesa.value;

        if (!novaMesaId) {
            exibirModalErro('Por favor, selecione uma mesa válida.');
            return;
        }

        btnConfirmar.disabled = true;
        btnConfirmar.textContent = 'Salvando...';

        // Requisição para atualizar a FK_Mesa_ID_Mesa
        fetch('../admin/api/api_atualizarstatus.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id: reserva_id,
                status: linha?.querySelector('.status-reserva')?.textContent.trim() ?? 'Em Atendimento',
                mesa: novaMesaId
            })
        })
        .then(async response => {
            const texto = await response.text();
            if (!texto) throw new Error("Resposta vazia do servidor.");
            return JSON.parse(texto);
        })
        .then(data => {
            if (data.sucesso) {
                exibirModalSucesso('Mesa alterada com sucesso!');
                body.removeChild(modal);
                atualizarReservas();
            } else {
                
                exibirModalErro(data.erro || 'Erro ao alterar a mesa.');
                btnConfirmar.disabled = false;
                btnConfirmar.textContent = 'Confirmar';
            }
        })
        .catch(error => {
            
            exibirModalErro('Falha na conexão com o servidor.');
            btnConfirmar.disabled = false;
            btnConfirmar.textContent = 'Confirmar';
        });
    });

    // Ação ao cancelar o modal
    btnCancelar.addEventListener('click', () => {
        body.removeChild(modal);
    });
}

function sair() {
    const modal = document.createElement('div');
    modal.style = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
        background-color: rgba(0, 0, 0, 0.30); display: flex;
        justify-content: center; align-items: center; z-index: 9999; font-size:20px;
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

    body.appendChild(modal);

    modal.querySelector('.btn-confirmar').addEventListener('click', () => {
        body.removeChild(modal);
        window.location.href = '../admin/logout.php';
    });

    modal.querySelector('.btn-cancelar').addEventListener('click', () => {
        body.removeChild(modal);
    });
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