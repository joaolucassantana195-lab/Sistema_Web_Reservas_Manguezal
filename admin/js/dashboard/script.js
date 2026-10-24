const body = document.querySelector('body')
const containerTotal = document.querySelector('#total');
const containerAguardando = document.querySelector('#aguardando');
const containerConfirmadas = document.querySelector('#confirmadas');
const containerPrevisao = document.querySelector('#previsao');
const corpoTabela = document.querySelector("#body-reservas");
const errorDiv = document.querySelector('.sem-reservas');

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
            slider.scrollLeft = scrollLeft - (x - startX) * 1.5;
        });
    });
}

function permitirAlterarMesa(status, reserva_id) {
    if (status === 'Confirmada' || status === 'Em Atendimento') {
        return `<span onclick="alterarMesa(event, ${reserva_id})" class="btn-alterar-mesa"><img src="images/alterarMesa.png"></span>`;
    }
    return '';
}

function alterarMesa(e, reserva_id) {
    e.preventDefault();

    const linha = e.target.closest('.linha-reservas');
    const qtdPessoas = linha?.querySelector('#celula-qtdpessoas')?.textContent || 1;
    const dataHoje = new Date().toLocaleDateString('sv-SE');

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

    fetch(`../admin/api/api_mesas_select.php?data=${dataHoje}`)
        .then(r => {
            if (!r.ok) throw new Error("Erro ao buscar mesas.");
            return r.json();
        })
        .then(mesas => {
            selectMesa.innerHTML = '';

            const disponiveis = mesas.filter(m =>
                m.ocupacao === 'Livre' &&
                m.mesa_ativa == true &&
                m.mesa_capacidade >= parseInt(qtdPessoas)
            );

            if (disponiveis.length === 0) {
                selectMesa.innerHTML = `<option value="">Nenhuma mesa disponível para este tamanho de grupo.</option>`;
                btnConfirmar.disabled = true;
                btnConfirmar.style.opacity = '0.5';
            } else {
                selectMesa.innerHTML = `<option value="">Selecione uma mesa...</option>`;
                disponiveis.forEach(m => {
                    const localMesa = m.mesa_local ? m.mesa_local.toUpperCase() : '';
                    selectMesa.innerHTML += `<option value="${m.mesa_id}">Mesa ${limparTexto(m.mesa_numero)} ${limparTexto(localMesa)} (Máx. ${m.mesa_capacidade} pessoas)</option>`;
                });
            }
        })
        .catch(error => {
            exibirModalErro('Não foi possível carregar as mesas disponíveis.');
            body.removeChild(modal);
        });

    btnConfirmar.addEventListener('click', () => {
        const novaMesaId = selectMesa.value;

        if (!novaMesaId) {
            exibirModalErro('Por favor, selecione uma mesa válida.');
            return;
        }

        btnConfirmar.disabled = true;
        btnConfirmar.textContent = 'Salvando...';

        fetch('../admin/api/api_atualizarstatus.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
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
                atualizarDashboard();
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

    btnCancelar.addEventListener('click', () => {
        body.removeChild(modal);
    });
}

function atualizarDashboard() {
    let clientesAguardando = [];
    let reservasConfirmadas = [];
    let totalPessoas = 0;

    fetch('../admin/api/api_dashboard.php')
        .then(response => response.json())
        .then(reservas => {
            let htmlConteudo = '';

            reservas.forEach(element => {
                let horarioFormatado = element.reserva_horario ? element.reserva_horario.slice(0, -3) : '';
                
                // Formatação da classe de status
                let statusClass = element.reserva_status
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f\s]/g, '');

                // Verifica se deve exibir o botão com base na variável 'userPerfil'
                let botaoVerLista = '';
                if (typeof userPerfil !== 'undefined' && userPerfil !== 'atendente') {
                    botaoVerLista = `
                            <button class="btn-td-reserva" onclick="verReserva(event)">Ver na lista</button>
                    `;
                }

                // Acumula o HTML na string
                htmlConteudo += `
                    <div class="linha-reservas">
                    <div class="celula-reservas td-status"><span class="status-${limparTexto(statusClass)} status-reserva">${limparTexto(element.reserva_status)}</span></div>
                    <div class="celula-reservas">
                    <h3>${limparTexto(element.cliente_nome)}</h3>
                    </div>
                        <div class="container-celula">
                            <div class="celula-reservas"><strong>HORÁRIO</strong><div>${horarioFormatado}</div></div>
                            <div class="celula-reservas"><strong>PESSOAS</strong><div id="celula-qtdpessoas">${element.reserva_qtd_pessoas}</div></div>
                            <div class="celula-reservas"><strong>MESA</strong><div class="container-numero"><div class="mesa-numero" style='padding: 5px 10px ; color: white;'>${limparTexto(element.mesa_numero)} ${limparTexto(element.mesa_local ? element.mesa_local.toUpperCase() : '')}</div>${permitirAlterarMesa(element.reserva_status, element.reserva_id)}</div></div>
                        </div>
                        <div style="width:100%; display:flex; justify-content:space-between; gap:5px; margin-top:5px;">
                        <button class="btn-obs" onclick="verObservacoes('${limparTexto(element.cliente_nome)}','${limparTexto(element.reserva_observacao)}')">Observações</button>
                        ${botaoVerLista}
                        </div>
                    </div>
                `;

                // Agrupamento e contagem de status
                if (element.reserva_status === 'Aguardando') {
                    clientesAguardando.push(element);
                } else if (element.reserva_status === 'Confirmada' || element.reserva_status === 'Em Atendimento') {
                    reservasConfirmadas.push(element);
                    totalPessoas += Number(element.reserva_qtd_pessoas);
                }
            });

            // Atualiza o DOM apenas uma vez
            corpoTabela.innerHTML = htmlConteudo;
            containerTotal.innerHTML = `<p>${reservas.length}</p>`;
            containerAguardando.innerHTML = `<p>${clientesAguardando.length}</p>`;
            containerConfirmadas.innerHTML = `<p>${reservasConfirmadas.length}</p>`;
            containerPrevisao.innerHTML = `<p>${totalPessoas}</p>`;

            // Ativa o evento de arraste após a renderização dos elementos
            if (typeof ativarArrasteNosContaineres === 'function') {
                ativarArrasteNosContaineres();
            }

            // Exibição da mensagem de erro/vazio
            errorDiv.style.display = reservas.length !== 0 ? 'none' : 'flex';
        })
        .catch((err) => {
            console.error('Erro ao atualizar o dashboard');
            errorDiv.style.display = 'flex';
        });
}

setInterval(atualizarDashboard,5000);
atualizarDashboard();

function verReserva(e){
    e.preventDefault()

    let data = new Date();
    let dataAtual = data.toLocaleDateString('sv-SE');
    
   window.location.href = `reservas.php?data=${dataAtual}`
}

function verObservacoes(nome, observacao){
    const modal = document.createElement('div');
    modal.style = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.30); display: flex;
        justify-content: center; align-items: center; z-index: 9999; font-size:20px;
    `;
   modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; margin:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036; width:600px; min-width:250px;">
            <p><strong>Observações</strong></p>
            <p>Cliente: ${limparTexto(nome)}</p>
            <div class='observacao-text'>
                <p>${limparTexto(observacao) || 'Nenhuma observação informada.'}</p>
            </div>
            <button class="btn-fechar">Fechar</button>
        </div>
    `;

    body.appendChild(modal);
    modal.querySelector('.btn-fechar').addEventListener('click', () => {
        body.removeChild(modal);
    }); 
}

function sair(){
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
    font-size:20px;
    `
    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px  #00000036;">
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
