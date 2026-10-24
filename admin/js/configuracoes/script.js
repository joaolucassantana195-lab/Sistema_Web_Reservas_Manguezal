// ============================================================
// NOTIFICAÇÕES
// ============================================================

function obterFeriados() {
  const feriados = [];

  const feriadosItens = document.querySelectorAll(
    ".container-feriados-grid .feriado-item"
  );

  feriadosItens.forEach((feriadoItem) => {
    const data = feriadoItem.querySelector(
      'input[name="data-feriado"]'
    );

    const descricao = feriadoItem.querySelector(
      'input[name="data-descricao"]'
    );

    const recorrente = feriadoItem.querySelector(
      'input[name="feriado-recorrente"]'
    );

    // Ignora feriado sem data
    if (!data || !data.value) {
      return;
    }

    feriados.push({
      data: data.value,
      descricao: descricao ? descricao.value : "",
      recorrente: recorrente ? recorrente.checked : false
    });
  });


  return feriados;
}

function obterContainerNotificacoes() {
  let container = document.querySelector(".container-notificacoes");

  if (!container) {
    container = document.createElement("div");
    container.classList.add("container-notificacoes");
    document.body.appendChild(container);
  }

  return container;
}

function exibirModalSucesso(mensagem) {
  const container = obterContainerNotificacoes();

  const div = document.createElement("div");
  div.classList.add("modal-confirm");
  div.innerText = mensagem;

  container.appendChild(div);

  setTimeout(() => {
    div.classList.add("saindo");

    setTimeout(() => {
      if (div.parentNode) {
        div.parentNode.removeChild(div);
      }
    }, 400);
  }, 2000);
}

function exibirModalErro(mensagem) {
  const container = obterContainerNotificacoes();

  const div = document.createElement("div");
  div.classList.add("modal-erro");

  div.innerText = mensagem || "Ocorreu um erro inesperado!";

  container.appendChild(div);

  setTimeout(() => {
    div.classList.add("saindo");

    setTimeout(() => {
      if (div.parentNode) {
        div.parentNode.removeChild(div);
      }
    }, 400);
  }, 2500);
}

// ============================================================
// MENU
// ============================================================

function abrirMenu(e) {
  const containerMenu = document.querySelector(".menu");
  const image = e.currentTarget.querySelector("img");

  if (containerMenu && image) {
    const estaAtivo = containerMenu.classList.toggle("ativo");

    image.src = estaAtivo
      ? "images/icone-closemenu.png"
      : "images/icone-menu.png";
  }
}

// ============================================================
// LOGOUT
// ============================================================

function sair() {
  const modal = document.createElement("div");

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
        <div class="varela-round-regular"
            style="
                background-color:#ececec;
                color:#00608a;
                padding:20px;
                border-radius:15px;
                border:2px solid #00608a;
                font-weight:400;
                box-shadow:0px 10px 15px #00000036;
            ">

            <p>Deseja fazer Logout do sistema?</p>

            <div
                class="divconcluir-acoes"
                style="
                    width:100%;
                    display:flex;
                    justify-content:end;
                    gap:10px;
                "
            >

                <button
                    class="btn-confirmar"
                    style="
                        background:#00608a;
                        color:white;
                        border:none;
                        padding:4px 12px;
                        border-radius:5px;
                        font-weight:bold;
                        cursor:pointer;
                    "
                >
                    Confirmar
                </button>

                <button
                    class="btn-cancelar"
                    style="
                        background:#dc3545;
                        color:white;
                        border:none;
                        padding:4px 12px;
                        border-radius:5px;
                        font-weight:bold;
                        cursor:pointer;
                    "
                >
                    Cancelar
                </button>

            </div>
        </div>
    `;

  document.body.appendChild(modal);

  modal.querySelector(".btn-confirmar").addEventListener("click", () => {
    document.body.removeChild(modal);

    window.location.href = '../admin/logout.php';
  });

  modal.querySelector(".btn-cancelar").addEventListener("click", () => {
    document.body.removeChild(modal);
  });
}

function adicionarFeriado(){
    const divGridFeriados = document.querySelector('.container-feriados-grid');

    divGridFeriados.innerHTML += ` <div class="feriado-item">
                      <div class="d-flex justify-content-end">
                        <button class="remover-feriado" onclick="removerFeriado(event)"><span>x</span></button>
                      </div>
                     <div class="container-feriados">
                        <label class="opcao-label">Data: </label>
                        <input type="date" name="data-feriado" id="data-feriado"> 
                        <label class="opcao-label">Descricao: </label>
                        <input type="text" name="data-descricao" id="data-descricao" maxlength="200" placeholder="Ex: Motivo do feriado"> 
                        <div>
                          <label class="opcao-label">Recorrente: </label>
                          <input type="checkbox" name="feriado-recorrente" id="feriado-recorrente">
                        </div>
                    </div>
                    </div>
    `

}

function removerFeriado(e){
    const divFeriado = e.target.closest(".feriado-item");
    divFeriado.remove();

}

// ============================================================
// HORÁRIOS DOS DIAS
// ============================================================

function configurarHorariosDias() {
  const dias = document.querySelectorAll("#dias-semana .dia-item");

  dias.forEach((diaItem) => {
    const checkbox = diaItem.querySelector('input[type="checkbox"]');

    const containerHorarios = diaItem.querySelector(".container-horarios");

    if (!checkbox || !containerHorarios) {
      return;
    }

    function atualizarVisibilidade() {
      if (checkbox.checked) {
        containerHorarios.style.display = "flex";
      } else {
        containerHorarios.style.display = "none";
      }
    }

    checkbox.addEventListener("change", atualizarVisibilidade);

    atualizarVisibilidade();
  });
}

// ============================================================
// OBTER HORÁRIOS DOS DIAS SELECIONADOS
// ============================================================

function obterHorariosFuncionamento() { 
    const horariosFuncionamento = {};
     const dias = document.querySelectorAll( "#dias-semana .dia-item" );
      
       dias.forEach(diaItem => { const checkbox = diaItem.querySelector( 'input[type="checkbox"]' );
         if (!checkbox) { 
            
             return; 

         } const dia = checkbox.value;
           // Só adiciona dias marcados
            if (!checkbox.checked) { 
                return;

             } 
             const abertura = diaItem.querySelector( 'input[name="horario_abertura[' + dia + ']"]' );
              const fechamento = diaItem.querySelector( 'input[name="horario_fechamento[' + dia + ']"]' );
               
                horariosFuncionamento[dia] = { abertura: abertura ? abertura.value : "", fechamento: fechamento ? fechamento.value : "" }; });
                 
                  return horariosFuncionamento; 
}

// ============================================================
// APLICAR HORÁRIOS VINDOS DO BANCO
// ============================================================

function aplicarHorariosFuncionamento(dados) {
  let horarios = dados;

  // Caso o PHP/API retorne JSON como string
  if (typeof horarios === "string") {
    try {
      horarios = JSON.parse(horarios);
    } catch (e) {
      

      horarios = {};
    }
  }

  if (!horarios || typeof horarios !== "object" || Array.isArray(horarios)) {
    horarios = {};
  }

  const dias = document.querySelectorAll("#dias-semana .dia-item");

  dias.forEach((diaItem) => {
    const checkbox = diaItem.querySelector('input[type="checkbox"]');

    if (!checkbox) {
      return;
    }

    const dia = checkbox.value;

    const abertura = diaItem.querySelector(`#horario-abertura-${dia}`);

    const fechamento = diaItem.querySelector(`#horario-fechamento-${dia}`);

    // Primeiro limpa os valores
    if (abertura) {
      abertura.value = "";
    }

    if (fechamento) {
      fechamento.value = "";
    }

    // Desmarca
    checkbox.checked = false;

    // Verifica se o dia existe no JSON
    if (Object.prototype.hasOwnProperty.call(horarios, dia)) {
      checkbox.checked = true;

      const horario = horarios[dia];

      if (horario && typeof horario === "object") {
        if (abertura) {
          abertura.value = horario.abertura || "";
        }

        if (fechamento) {
          fechamento.value = horario.fechamento || "";
        }
      }
    }
  });

  // Atualiza a exibição dos containers
  configurarHorariosDias();
}

function exibirFeriados(data, descricao, recorrente){
    const divGridFeriados = document.querySelector('.container-feriados-grid');
         divGridFeriados.innerHTML += ` <div class="feriado-item">
                      <div class="d-flex justify-content-end">
                        <button class="remover-feriado" onclick="removerFeriado(event)"><span>x</span></button>
                      </div>
                     <div class="container-feriados">
                        <label class="opcao-label">Data: </label>
                        <input type="date" name="data-feriado" id="data-feriado" value="${data}"> 
                        <label class="opcao-label">Descricao: </label>
                        <input type="text" name="data-descricao" id="data-descricao" maxlength="200" placeholder="Ex: Motivo do feriado" value="${descricao}"> 
                        <div>
                          <label class="opcao-label">Recorrente: </label>
                          <input type="checkbox" name="feriado-recorrente" id="feriado-recorrente" ${(recorrente == 1)?'checked' : null}>
                        </div>
                    </div>
                    </div>
    `
   
}

// ============================================================
// BUSCAR CONFIGURAÇÕES
// ============================================================
async function buscarDadosLogin(){
  const pEmail = document.querySelector('#email');

  try{
    const response = await fetch('../admin/api/api_dados_admin.php');
    const dados = await response.json();
    dados.forEach(element => {
      if(pEmail){
        pEmail.textContent = element.Email;
      }
    })
    return dados;
  }catch(err){
    console.log('Erro na requisição')
  }  
 
}

buscarDadosLogin();

function buscarConfigs() {
  const minPessoas = document.querySelector("#minimo-pessoas");

  const maxPessoas = document.querySelector("#maximo-pessoas");

  const maxReservas = document.querySelector("#maximo-reservas");

  const antecedenciaMaxima = document.querySelector("#antecedencia-maxima");

  const antecedenciaMinima = document.querySelector("#antecedencia-minima");

  const horarioUltimaReserva = document.querySelector(
    "#horario-ultima-reserva",
  );

  const toleranciaAtraso = document.querySelector("#tolerancia-atraso");

  const opcaoBeach = document.querySelector("#opcao-beach");

  const taxaBeach = document.querySelector("#taxa-beach");

  const confirmacaoAutomatica = document.querySelector(
    "#confirmacao-automatica",
  );

  const emailConfirmacao = document.querySelector("#email-confirmacao");

  const enviarLembrete = document.querySelector("#enviar-lembrete");

  const antecedenciaLembrete = document.querySelector("#antecedencia-lembrete");

  fetch("../admin/api/api_configuracoes.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erro na resposta do servidor");
      }

      return response.json();
    })

    .then((element) => {
      // ================================================
      // LIMITES
      // ================================================

      if (minPessoas) {
        minPessoas.value = element.min_pessoas_reserva;
      }

      if (maxPessoas) {
        maxPessoas.value = element.max_pessoas_reserva;
      }

      if (maxReservas) {
        maxReservas.value = element.limite_max_reservas_dia;
      }

      if (antecedenciaMaxima) {
        antecedenciaMaxima.value = element.antecedencia_max_reserva_dias;
      }

      // ================================================
      // DIAS + HORÁRIOS
      // ================================================

      aplicarHorariosFuncionamento(element.dias_funcionamento);

      // ================================================
      // OUTRAS CONFIGURAÇÕES
      // ================================================

      if (antecedenciaMinima) {
        antecedenciaMinima.value = element.antecedencia_min_reserva_horas;
      }

      if (horarioUltimaReserva) {
        horarioUltimaReserva.value = element.horario_limite_ultima_reserva;
      }

      if (toleranciaAtraso) {
        toleranciaAtraso.value = element.tolerancia_atraso_minutos;
      }

      // ================================================
      // BEACH CLUB
      // ================================================

      if (opcaoBeach) {
        opcaoBeach.value =
          element.permitir_reserva_beach_club == 0 ? "nao" : "sim";
      }

      if (taxaBeach) {
        taxaBeach.value = element.taxa_pessoa_beach_club;
      }

      // ================================================
      // CONFIRMAÇÃO AUTOMÁTICA
      // ================================================

      if (confirmacaoAutomatica) {
        confirmacaoAutomatica.checked =
          element.confirmar_reservas_automaticamente == 1;
      }

      // ================================================
      // EMAIL CONFIRMAÇÃO
      // ================================================

      if (emailConfirmacao) {
        emailConfirmacao.checked = element.enviar_email_confirmacao == 1;
      }

      // ================================================
      // LEMBRETE
      // ================================================

      if (enviarLembrete) {
        enviarLembrete.checked = element.enviar_email_lembrete == 1;
      }

      if (antecedenciaLembrete) {
        antecedenciaLembrete.value = element.horas_antecedencia_lembrete;
      }
    })

    .catch((error) => {
      

      exibirModalErro("Erro de conexão ao buscar configurações");
    });
}

function buscarFeriados(){
     fetch("../admin/api/api_feriados.php")
     .then((response) => {
      if (!response.ok) {
        throw new Error("Erro na resposta do servidor");
      }

      return response.json();
    })
     .then(element => {
        element.feriados.forEach(feriado => {
             exibirFeriados(feriado.data, feriado.descricao, feriado.recorrente);
        }

        )
           
     })
      .catch((error) => {
      

      exibirModalErro("Erro de conexão ao buscar feriados");
    });

}

// ============================================================
// SALVAR ALTERAÇÕES
// ============================================================

function salvarAlteracoes() { 
    const minPessoas = document.querySelector("#minimo-pessoas") ? document.querySelector("#minimo-pessoas").value : "";
     const maxPessoas = document.querySelector("#maximo-pessoas") ? document.querySelector("#maximo-pessoas").value : "";
      const maxReservas = document.querySelector("#maximo-reservas") ? document.querySelector("#maximo-reservas").value : "";
       const antecedenciaMaxima = document.querySelector("#antecedencia-maxima") ? document.querySelector("#antecedencia-maxima").value : "";
        const antecedenciaMinima = document.querySelector("#antecedencia-minima") ? document.querySelector("#antecedencia-minima").value : "";
         const horarioUltimaReserva = document.querySelector("#horario-ultima-reserva") ? document.querySelector("#horario-ultima-reserva").value : "";
          const toleranciaAtraso = document.querySelector("#tolerancia-atraso") ? document.querySelector("#tolerancia-atraso").value : "";
           const inputBeach = document.querySelector("#opcao-beach") ? document.querySelector("#opcao-beach").value : "";
            const taxaBeach = document.querySelector("#taxa-beach") ? document.querySelector("#taxa-beach").value : "";
             const inputConfirmacaoAutomatica = document.querySelector("#confirmacao-automatica");
              const inputEmailConfirmacao = document.querySelector("#email-confirmacao");
               const inputEnviarLembrete = document.querySelector("#enviar-lembrete"); const
                antecedenciaLembrete = document.querySelector("#antecedencia-lembrete") ? document.querySelector("#antecedencia-lembrete").value : "";
                  // ================================================ // CONVERTER CHECKBOXES // ================================================ 
                  const opcaoBeach = inputBeach === "sim" ? 1 : 0;
                   const confirmacaoAutomatica = inputConfirmacaoAutomatica && inputConfirmacaoAutomatica.checked ? 1 : 0;
                    const emailConfirmacao = inputEmailConfirmacao && inputEmailConfirmacao.checked ? 1 : 0;
                     const enviarLembrete = inputEnviarLembrete && inputEnviarLembrete.checked ? 1 : 0;
                      // ================================================ // PAYLOAD // ================================================ 
                      const horariosFuncionamento = obterHorariosFuncionamento();
                      const feriados = obterFeriados();

const payload = {
  minPessoas: minPessoas,
  maxPessoas: maxPessoas,
  maxReservas: maxReservas,
  antecendenciaMax: antecedenciaMaxima,
  antecedenciaMin: antecedenciaMinima,
  horarioUltimaReserva: horarioUltimaReserva,
  toleranciaAtraso: toleranciaAtraso,
  opcaoBeach: opcaoBeach,
  taxaBeach: taxaBeach,
  confirmacaoAutomatica: confirmacaoAutomatica,
  emailConfirmacao: emailConfirmacao,
  enviarLembrete: enviarLembrete,
  lembrete: antecedenciaLembrete,
  dias: horariosFuncionamento,
  feriados: feriados
};

    
                 // ================================================ // ENVIAR PARA API // ================================================ 
                 fetch("../admin/api/api_salvarconfiguracoes.php", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload) }) .then(response => { if (!response.ok) { throw new Error( "Erro HTTP: " + response.status ); }
                  // IMPORTANTE: // Primeiro pegamos a resposta como texto // para descobrir o que o PHP realmente retornou. 
                  return response.text(); }) .then(texto => {
                     
                    
                    // Agora tentamos converter para JSON 
                    let data;
                     try { data = JSON.parse(texto);

                      } catch (erro) { console.error( "A API NÃO retornou um JSON válido." ); 
                        
                         
                        } 
                        // ============================================ // PROCESSAR RESPOSTA // ============================================ 
                        if (data.sucesso) { exibirModalSucesso( data.mensagem || "Configurações salvas com sucesso!" );
                             buscarConfigs();
                             } else { 
                                exibirModalErro( "Erro ao salvar: " + ( data.erro || "Falha ao processar requisição" ) );
                             } }).catch(error => { console.error( "Erro na requisição");
                                 exibirModalErro( "Erro de conexão ao salvar alterações" ); 
                                }); 
                    }

// ============================================================
// RESTAURAR PADRÕES
// ============================================================

function restaurarPadroes() {
  const modal = document.createElement("div");

  modal.style = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.30);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        font-size: 20px;
    `;

  modal.innerHTML = `
        <div
            class="varela-round-regular"
            style="
                background-color:#ececec;
                color:#00608a;
                padding:20px;
                margin:20px;
                border-radius:15px;
                border:2px solid #00608a;
                font-weight:400;
                box-shadow:0px 10px 15px #00000036;
            "
        >

            <p>
                Deseja restaurar as configurações?
            </p>

            <div
                class="divconcluir-acoes"
                style="
                    width:100%;
                    display:flex;
                    justify-content:end;
                    gap:10px;
                "
            >

                <button
                    class="btn-confirmar"
                    style="
                        background:#00608a;
                        color:white;
                        border:none;
                        padding:4px 12px;
                        border-radius:5px;
                        font-weight:bold;
                        cursor:pointer;
                    "
                >
                    Confirmar
                </button>

                <button
                    class="btn-cancelar"
                    style="
                        background:#dc3545;
                        color:white;
                        border:none;
                        padding:4px 12px;
                        border-radius:5px;
                        font-weight:bold;
                        cursor:pointer;
                    "
                >
                    Cancelar
                </button>

            </div>

        </div>
    `;

  document.body.appendChild(modal);

  // ================================================
  // CANCELAR
  // ================================================

  modal.querySelector(".btn-cancelar").addEventListener("click", () => {
    document.body.removeChild(modal);
  });

  // ================================================
  // CONFIRMAR
  // ================================================

  modal.querySelector(".btn-confirmar").addEventListener("click", () => {
    document.body.removeChild(modal);

    fetch("../admin/api/api_restaurarpadroes.php", {
      method: "POST",
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Erro HTTP: " + response.status);
        }

        return response.json();
      })

      .then((data) => {
        if (data.sucesso) {
          exibirModalSucesso(
            data.mensagem || "Padrões restaurados com sucesso!",
          );

          // ====================================
          // VALORES PADRÃO
          // ====================================

          if (document.querySelector("#minimo-pessoas")) {
            document.querySelector("#minimo-pessoas").value = 20;
          }

          if (document.querySelector("#maximo-pessoas")) {
            document.querySelector("#maximo-pessoas").value = 50;
          }

          if (document.querySelector("#maximo-reservas")) {
            document.querySelector("#maximo-reservas").value = 40;
          }

          if (document.querySelector("#antecedencia-maxima")) {
            document.querySelector("#antecedencia-maxima").value = 30;
          }

          if (document.querySelector("#antecedencia-minima")) {
            document.querySelector("#antecedencia-minima").value = 6;
          }

          if (document.querySelector("#horario-ultima-reserva")) {
            document.querySelector("#horario-ultima-reserva").value = "16:00";
          }

          if (document.querySelector("#tolerancia-atraso")) {
            document.querySelector("#tolerancia-atraso").value = 15;
          }

          if (document.querySelector("#opcao-beach")) {
            document.querySelector("#opcao-beach").value = "sim";
          }

          if (document.querySelector("#taxa-beach")) {
            document.querySelector("#taxa-beach").value = 30;
          }

          if (document.querySelector("#confirmacao-automatica")) {
            document.querySelector("#confirmacao-automatica").checked = true;
          }

          if (document.querySelector("#email-confirmacao")) {
            document.querySelector("#email-confirmacao").checked = true;
          }

          if (document.querySelector("#enviar-lembrete")) {
            document.querySelector("#enviar-lembrete").checked = true;
          }

          if (document.querySelector("#antecedencia-lembrete")) {
            document.querySelector("#antecedencia-lembrete").value = 6;
          }

          // ====================================
          // DIAS PADRÃO
          // ====================================

          const dias = document.querySelectorAll("#dias-semana .dia-item");

          dias.forEach((diaItem) => {
            const checkbox = diaItem.querySelector('input[type="checkbox"]');

            if (!checkbox) {
              return;
            }

            checkbox.checked = true;

            const dia = checkbox.value;

            const abertura = diaItem.querySelector(`#horario-abertura-${dia}`);

            const fechamento = diaItem.querySelector(
              `#horario-fechamento-${dia}`,
            );

            if (abertura) {
              abertura.value = "09:00";
            }

            if (fechamento) {
              fechamento.value = "17:00";
            }
          });

          configurarHorariosDias();

          // Busca novamente do banco
          buscarConfigs();
        } else {
          exibirModalErro(
            "Erro ao restaurar: " +
              (data.erro || "Falha ao processar requisição"),
          );
        }
      })

      .catch((error) => {

        exibirModalErro("Erro de conexão ao restaurar padrões");
      });
  });
}

const cardlogin = document.querySelector('#card-login')

function editarEmail(e){

  cardlogin.innerHTML = `
                <div class="opcao">
                  <label class="opcao-label" for="email-admin">Novo email</label>
                  <span class="opcao-desc">Definir novo email de acesso.</span>
                  <input type="email" name="email-admin" id="email-admin" placeholder="Digite o email"/>
                </div>

                <div class="opcao">
                  <label class="opcao-label" for="senha-admin">Senha atual</label>
                  <span class="opcao-desc">Digite sua senha</span>
                  <input type="password" name="editar-email-senha" id="editar-email-senha" placeholder="Digite sua senha"/>
                </div>
                <span id="span-erro" class="erro"></span>
                <div class="opcao acoes-card-login">
                  <button class="btn btn-primary btn-alterar" onclick="alterarEmail(event)">Enviar<button>
                  <button class="btn btn-secondary btn-cancelar" onclick="cancelarEditar()">Cancelar<button>
                </div>
  `
  buscarDadosLogin();
}

function editarSenha(){
  cardlogin.innerHTML = `
                <div class="opcao">
                  <label class="opcao-label" for="email-admin">Email</label>
                  <span class="opcao-desc">Email de acesso ao painel administrativo.</span>
                  <div class="container-email d-flex gap-2">
                    <p id="email">adminemail@email.com</p>
                  </div>
                </div>

                <div class="opcao">
                  <label class="opcao-label" for="email-admin">Alterar Senha</label>
                  <span class="opcao-desc">Ao confirmar, será enviado um link de redefinição para o seu email.</span>
                </div>
                <div class="opcao acoes-card-login">
                  <button class="btn btn-primary btn-alterar" onclick="redefinirSenha(event)">Redefinir senha<button>
                  <button class="btn btn-secondary btn-cancelar" onclick="cancelarEditar()">Cancelar<button>
                </div>
  `

  buscarDadosLogin();

}

function cancelarEditar(){
   cardlogin.innerHTML = `<div class="opcao">
                  <label class="opcao-label" for="email-admin">Email</label>
                  <span class="opcao-desc">Email de acesso ao painel administrativo.</span>
                  <div class="container-email d-flex gap-2">
                    <p id="email">adminemail@email.com</p><span class="editar-credenciais" onclick="editarEmail()"><p>Editar</p></span>
                  </div>
                  <div class="container-senha d-flex gap-2">
                    <p>(Sua senha)</p><span class="editar-credenciais" onclick="editarSenha()"><p>Editar</p></span>
                  </div>
                  </div>
  `
  buscarDadosLogin();
}

// 1. Função para solicitar o token/link de redefinição de senha
async function redefinirSenha(e) {
  e.target.disabled = true;
  e.target.textContent = 'Enviando Link...'
    const emailElement = document.getElementById('email');
    const email = emailElement ? emailElement.textContent.trim() : '';

    if (!email) {
        exibirModalErro('E-mail não encontrado.');
        return;
    }

    try {
        const response = await fetch('../admin/api/api_enviar_senha_token_admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email})
        });

        const resultado = await response.json();

        if (resultado.sucesso) {
            cancelarEditar();
        }else{
          e.target.disabled = false;
          e.target.textContent = 'Redefinir senha'
        }

        exibirModalSucesso(resultado.mensagem)

    } catch (error) {
          e.target.disabled = false;
          e.target.textContent = 'Redefinir senha'
        console.error('Erro na requisição');
    }
}

// 2. Função para alterar o e-mail enviando a senha atual para confirmação
async function alterarEmail(e) {
  e.target.disabled = true
  e.target.textContent = "Enviando..."
  const spanErro = document.querySelector('#span-erro')
    const senhaAtual = document.getElementById('editar-email-senha').value.trim();
    const novoEmail = document.getElementById('email-admin').value.trim();

    const dadoslogin = await buscarDadosLogin();
    let emailAtual;
     dadoslogin.forEach(element => {
      emailAtual = element.Email;
    });

   if (!senhaAtual || !novoEmail) {
         spanErro.textContent = 'Preencha todos os campos!';
       	  e.target.disabled = false
          e.target.textContent = "Enviar"
        return;
    }

    try {
        
        const response = await fetch('../admin/api/api_enviar_email_codigo_admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: emailAtual, senha: senhaAtual, novoEmail : novoEmail })
        });

        const resultado = await response.json();
        spanErro.textContent = resultado.mensagem;

        if (resultado.sucesso) {
            cardlogin.innerHTML =`
                <div class="opcao">
                  <label class="opcao-label" for="email-admin">Email</label>
                  <span class="opcao-desc">Email a ser adicionado.</span>
                  <div class="container-email d-flex gap-2">
                    <p id="email">${novoEmail}</p>
                  </div>
                </div>
                <div class="opcao">
                  <label class="opcao-label" for="codigo-email">Código de verificação</label>
                  <span class="opcao-desc">Digite o código enviado ao seu email</span>
                  <input type="password" name="editar-email-codigo" id="editar-email-codigo" placeholder="Código de verificação"/>
                  <span class="editar-credenciais" onclick="reenviarCodigo(event, '${novoEmail}')">Reenviar código</span>
                </div>
                <span id="span-erro" class="erro"></span>
                <div class="opcao acoes-card-login">
                  <button class="btn btn-primary btn-alterar" onclick="validarCodigoEmail(event)">Alterar email<button>
                  <button class="btn btn-secondary btn-cancelar" onclick="cancelarEditar()">Cancelar<button>
                </div>`
                
        }
        
        e.target.disabled = false
        e.target.textContent = "Enviar"
    } catch (error) {
       e.target.disabled = false
       e.target.textContent = "Enviar"
        console.error('Erro ao conectar com o servidor');
    }
}

async function validarCodigoEmail(e){
  e.target.disabled = true;
  e.target.textContent = 'Alterando...'
  const iptCodigo = document.querySelector('#editar-email-codigo').value.trim();
  const novoEmail = document.querySelector('#email').textContent.trim();
  const spanErro = document.querySelector('#span-erro');

  try{
    const response = await fetch('../admin/api/api_validar_codigo_email.php', {
      method:'POST',
      headers:{
        'Content-Type':'application/json'
      },
      body:JSON.stringify({
        codigo : iptCodigo,
        novoEmail : novoEmail
      })
    })

    const resposta = await response.json();
    
      e.target.disabled = false
      e.target.textContent = "Alterar email"

      if(resposta.sucesso){
         cardlogin.innerHTML = `<div class="opcao">
                  <label class="opcao-label" for="email-admin">Email</label>
                  <span class="opcao-desc">Email de acesso ao painel administrativo.</span>
                  <div class="container-email d-flex gap-2">
                    <p id="email">adminemail@email.com</p><span class="editar-credenciais" onclick="editarEmail()"><p>Editar</p></span>
                  </div>
                  <div class="container-senha d-flex gap-2">
                    <p>(Sua senha)</p><span class="editar-credenciais" onclick="editarSenha()"><p>Editar</p></span>
                  </div>
                  </div>
          `
          exibirModalSucesso(resposta.mensagem);
          buscarDadosLogin();
      }else{
        spanErro.textContent = resposta.mensagem
      }

  }catch(err){
      e.target.disabled = false
       e.target.textContent = "Alterar email"
        console.error('Erro na requisição');
  }

}

async function reenviarCodigo(e, novoEmail) {
  const spanErro = document.querySelector('#span-erro');
  
  if (!novoEmail) {
    if (spanErro) spanErro.textContent = 'E-mail não encontrado para reenvio.';
    return;
  }

  const botao = e.target;

  // Desabilita temporariamente o clique e muda o texto para o estado de envio
  botao.style.pointerEvents = 'none';
  botao.textContent = 'Reenviando...';
  botao.style.color = 'gray';

  try {
    const response = await fetch('../admin/api/api_reenviar_email_codigo_admin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ novoEmail: novoEmail })
    });

    const resultado = await response.json();

    if (resultado.sucesso) {
      if (typeof exibirModalSucesso === 'function') {
        exibirModalSucesso(resultado.mensagem);
      }
      
      // Se deu sucesso, inicia o bloqueio de 1 minuto (60 segundos) com contagem visível
      iniciarContagemRegressiva(botao, 60);

    } else {
      if (spanErro) {
        spanErro.textContent = resultado.mensagem;
      }
      // Se deu erro na regra de negócio, libera o botão imediatamente
      botao.style.pointerEvents = 'auto';
      botao.textContent = 'Reenviar código';
      botao.style.color = '';
    }

  } catch (error) {
    console.error('Erro na requisição:');
    if (spanErro) {
      spanErro.textContent = 'Erro ao conectar com o servidor para reenviar o código.';
    }
    // Libera o botão em caso de erro na requisição
    botao.style.pointerEvents = 'auto';
    botao.textContent = 'Reenviar código';
    botao.style.color = '';
  }
}

// Função auxiliar para gerenciar a contagem regressiva segundo a segundo
function iniciarContagemRegressiva(botao, segundosTotais) {
  let tempoRestante = segundosTotais;

  const intervalo = setInterval(() => {
    let minutos = Math.floor(tempoRestante / 60);
    let segundos = tempoRestante % 60;

    let minFormatado = String(minutos).padStart(2, '0');
    let segFormatado = String(segundos).padStart(2, '0');

    // Atualiza o texto do botão com o tempo entre parênteses
    botao.textContent = `Reenviar código (${minFormatado}:${segFormatado})`;

    // Quando o tempo zerar
    if (tempoRestante <= 0) {
      clearInterval(intervalo); // Para o setInterval
      botao.style.pointerEvents = 'auto';
      botao.textContent = 'Reenviar código';
      botao.style.color = ''; // Restaura a cor padrão
    }

    tempoRestante--;
  }, 1000); // Roda a cada 1 segundo (1000ms)
}


// ============================================================
// INICIALIZAÇÃO
// ============================================================

document.addEventListener("DOMContentLoaded", () => {
  configurarHorariosDias();

  buscarConfigs();
  buscarFeriados();
});