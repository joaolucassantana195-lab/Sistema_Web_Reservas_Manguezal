const bodyAtendentes = document.querySelector('.body-atendentes');
const errorDiv = document.querySelector('.nenhum-usuario');

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

function buscarUsuarios() {
    fetch('../admin/api/api_usuarios.php')
        .then(response => {
            if (!response.ok) throw new Error('Falha ao carregar usuários');
            return response.json();
        })
        .then(usuarios => {
            bodyAtendentes.innerHTML = '';
            usuarios.forEach(element => {
                let perfilFormatado = element.perfil ? element.perfil.charAt(0).toUpperCase() + element.perfil.slice(1) : '';
                bodyAtendentes.innerHTML += `
                    <div class="linha-atendente">
                        <div class="celula-atendente"><div><strong>Nome</strong></div><div>${limparTexto(element.nome)}</div></div>
                        <div class="celula-atendente"><div><strong>Perfil</strong></div><div>${limparTexto(perfilFormatado)}</div></div>
                        <div class="celula-atendente login"><div><strong>Login</strong></div><div>${limparTexto(element.login)}</div></div>
                        <div class="celula-atendente">
                            <div class="acoes-card">
                                <div class="btn-redefinir" onclick="redefinirSenha(event, ${element.id})">Redefinir senha</div>
                                <div class="btn-remover" onclick="removerUsuario(event, ${element.id})">Remover usuário</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            if (errorDiv) {
                errorDiv.style.display = usuarios.length === 0 ? 'flex' : 'none';
            }
        })
        .catch(error => {
            
            exibirModalErro('Erro ao carregar lista de usuários');
        });
}

setInterval(() => {
    buscarUsuarios();
}, 15000);

buscarUsuarios();

async function cadastrarUsuario(e) {
    if (e && e.preventDefault) {
        e.preventDefault();
    }
    e.target.disabled = true
    e.target.textContent = "Cadastrando..."

    const form = e ? e.target.closest('form') : null;
    const inputNome = document.querySelector('#nome');
    const inputLogin = document.querySelector('#login');
    const inputSenha = document.querySelector('#senha');
    const inputSenhaRepetida = document.querySelector('#senha-repetida');
    const inputPerfil = document.querySelector('#perfil');
    const spnerroAdicionar = document.querySelector('.spn-erro-adicionar');

    const nome = inputNome ? inputNome.value.trim() : '';
    const login = inputLogin ? inputLogin.value.trim() : '';
    const senha = inputSenha ? inputSenha.value : '';
    const senhaRepetida = inputSenhaRepetida ? inputSenhaRepetida.value : '';
    const perfil = inputPerfil ? inputPerfil.value : '';

    if (!nome || !login || !senha || !perfil || !senhaRepetida) {
        spnerroAdicionar.style.display = 'block';
        spnerroAdicionar.textContent = 'Por favor, preencha todos os campos acima.';
        e.target.disabled = false
        e.target.textContent = "Cadastrar"
        return;
    } else if (senha !== senhaRepetida) {
        spnerroAdicionar.style.display = 'block';
        spnerroAdicionar.textContent = 'As senhas não correspondem.';
        e.target.disabled = false
        e.target.textContent = "Cadastrar"
        return;
    }

    try {
        spnerroAdicionar.textContent = '';
        spnerroAdicionar.style.display = 'none';

        const response = await fetch('../admin/api/api_adicionarusuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nome, login, senha, perfil })
        });

        const data = await response.json();

        e.target.disabled = false
        e.target.textContent = "Cadastrar"

        if (data.sucesso) {
            exibirModalSucesso(data.mensagem || 'Usuário cadastrado com sucesso!');
            if (form) form.reset();
            inputNome.value = null
            inputLogin.value = null
            inputSenha.value = null
            inputSenhaRepetida.value = null
            inputPerfil.default
            buscarUsuarios();
        } else {
            exibirModalErro(data.erro || 'Erro ao cadastrar usuário');
        }
       

    } catch (error) {
        e.target.disabled = false
        e.target.textContent = "Cadastrar"
        
        exibirModalErro('Erro de conexão ao cadastrar usuário');
    }
}

function removerUsuario(e, id) {
    if (e) e.preventDefault();

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
    `;

    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036; display:flex; flex-direction:column; gap:10px; align-items:center;">
            <p style="margin:0;">Deseja realmente remover este usuário?</p>
            <div class="content-modal" style="width:100%; display:flex; justify-content:end; gap:10px; margin-top:10px;">
               <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    modal.querySelector('.btn-cancelar').addEventListener('click', () => {
        document.body.removeChild(modal);
    });

    modal.querySelector('.btn-confirmar').addEventListener('click', (e) => {
        e.preventDefault();

        fetch('../admin/api/api_removerusuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                exibirModalSucesso(data.mensagem || 'Usuário removido com sucesso!');
                buscarUsuarios();
            } else {
                exibirModalErro(data.erro || 'Erro ao remover usuário');
            }
        })
        .catch(error => {
            
            exibirModalErro('Erro de conexão ao remover usuário');
        })
        .finally(() => {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
        });
    });
}

function redefinirSenha(e, id) {
    if (e) e.preventDefault();

    const modal = document.createElement('div');
    modal.style = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        font-size: 20px;
    `;

    modal.innerHTML = `
        <div class="varela-round-regular" style="background-color:#ececec; color:#00608a; padding:20px; border-radius:15px; border:2px solid #00608a; font-weight:400; box-shadow:0px 10px 15px #00000036; display:flex; flex-direction:column; gap:10px; align-items:center;">
            <p style="margin:0;">Redefina a senha</p>
            <div style="display:flex; flex-direction:column;">
                <p style="font-size:15px; margin:5px 0;">Digite uma nova senha</p>
                <input type="password" placeholder="Insira a nova senha" id="input-novasenha">
                <p style="font-size:15px; margin:5px 0;">Repita a nova senha</p>
                <input type="password" placeholder="Repita a senha" id="input-senharepetida">
            </div>
            <span class="spn-erro" style="color:red; font-size:12px; display:none; text-align:start;"></span>
            <div class="content-modal" style="width:100%; display:flex; justify-content:space-between; gap:10px; margin-top:10px;">
                <button class="btn-confirmar" style="background:#00608a; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Confirmar</button>
                <button class="btn-cancelar" style="background:#dc3545; color:white; border:none; padding:4px 12px; border-radius:5px; font-weight:bold; cursor:pointer;">Cancelar</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    modal.querySelector('.btn-cancelar').addEventListener('click', () => {
        document.body.removeChild(modal);
    });

    modal.querySelector('.btn-confirmar').addEventListener('click', (e) => {
        e.preventDefault();
        const novaSenha = modal.querySelector('#input-novasenha').value;
        const senhaRepetida = modal.querySelector('#input-senharepetida').value;
        const spnerro = modal.querySelector('.spn-erro');
        spnerro.style.display = "block";
        spnerro.innerText = '';

        if (senhaRepetida && novaSenha && senhaRepetida === novaSenha) {
            
            
            fetch('../admin/api/api_redefinirsenhausuario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, novasenha: novaSenha })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    exibirModalSucesso(data.mensagem || 'Senha alterada com sucesso!');
                    buscarUsuarios();
                    document.body.removeChild(modal);
                } else {
                    spnerro.textContent = data.mensagem || 'Erro ao redefinir senha';
                }
            })
            .catch(error => {
                
                exibirModalErro('Erro de conexão ao redefinir senha');
                document.body.removeChild(modal);
            });
        } else {
            if (!senhaRepetida || !novaSenha) {
                spnerro.innerText = 'Preencha todos os campos!';
            } else {
                spnerro.innerText = 'As senhas não correspondem!';
            }
        }
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
        font-size:20px;
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